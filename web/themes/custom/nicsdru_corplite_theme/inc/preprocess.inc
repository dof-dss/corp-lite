<?php

/**
 * @file
 * Functions to support theming in the nicsdru_corplite_theme theme.
 */

use Drupal\Component\Utility\Html;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Render\Element;
use Drupal\Core\Render\Markup;
use Drupal\Core\StringTranslation\ByteSizeMarkup;
use Drupal\Core\Template\Attribute;
use Drupal\Core\Url;
use Drupal\file\FileInterface;
use Drupal\media\Entity\Media;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Drupal\taxonomy\Entity\Vocabulary;

/**
 * Implements hook_preprocess().
 */
function nicsdru_corplite_theme_preprocess(&$variables, $hook) {
  // Set a variable based on the path.matcher.
  if (!isset($variables['is_front'])) {
    try {
      $variables['is_front'] = \Drupal::service('path.matcher')->isFrontPage();
    }
    catch (\Exception $e) {
      $variables['is_front'] = FALSE;
    }
  }

  // Ensure the cache varies correctly.
  $variables['#cache']['contexts'][] = 'url.path.is_front';
}

/**
 * Implements hook_page_attachments_alter().
 */
function nicsdru_corplite_theme_page_attachments_alter(array &$page) {
  // Attach our drupal components library for authenticated users.
  $userCurrent = \Drupal::currentUser();

  if ($userCurrent->isAuthenticated()) {
    $page['#attached']['library'][] = 'nicsdru_corplite_theme/authenticated-styles';
  }

  // Remove the stock Drupal favicon if it is present.
  if (isset($page['#attached']['html_head_link'])) {
    foreach ($page['#attached']['html_head_link'] as $key => $config) {
      if (isset($config[0]['rel']) && $config[0]['rel'] === 'shortcut icon') {
        unset($page['#attached']['html_head_link'][$key]);
      }
    }
  }
  return $page;
}

/**
 * Implements hook_preprocess_page() for page.html.twig.
 */
function nicsdru_corplite_theme_preprocess_page(array &$variables) {
  // Set some render attributes for various regions on the page.html.twig
  // we can hook into these to add additional classes for layout in sub theming.
  $variables['main_content_attr'] = [
    'class' => ['container'],
    'id' => ['main-content'],
  ];
  $variables['sidebar_first_attr'] = [
    'class' => ['layout-sidebar-first'],
    'id' => ['first_sidebar'],
    'role' => 'presentation',
  ];
  $variables['content_wrapper_attr'] = [];
  $variables['sidebar_second_attr'] = [
    'class' => ['layout-sidebar-second'],
    'id' => ['second_sidebar'],
  ];

  // Declare region attributes.
  $variables['main_content_attr'] = new Attribute($variables['main_content_attr']);
  $variables['sidebar_first_attr'] = new Attribute($variables['sidebar_first_attr']);
  $variables['content_wrapper_attr'] = new Attribute($variables['content_wrapper_attr']);
  $variables['sidebar_second_attr'] = new Attribute($variables['sidebar_second_attr']);

  // Social links for footer.
  if (empty($variables['page']['footer'])) {
    // Set some key variables for correct template mapping if something else
    // hasn't already, eg: a block.
    $variables['page']['footer']['#region'] = 'footer';
    $variables['page']['footer']['#theme_wrappers'][] = 'region';
  }
}

/**
 * Implements hook_preprocess_pager().
 */
function nicsdru_corplite_theme_preprocess_pager(&$variables) {
  if (empty($variables['items'])) {
    return;
  }

  // Lowercase all text controls.
  foreach ($variables['items'] as $key => &$item) {
    if (array_key_exists('text', $item) && is_numeric($item['text']) === FALSE) {
      $item['text'] = strtolower($item['text']);
    }
  }
}

/**
 * Implements hook_preprocess_block().
 */
function nicsdru_corplite_theme_preprocess_block(array &$variables) {
  // Include social links as part of main/footer menu blocks to ease the
  // process of wrapping menu + links elements in a parent element to set
  // a max width on.
  if (preg_match('/^system_menu_block\:(main|footer)$/', $variables['plugin_id'])) {
    $social_links = _nicsdru_corplite_social_links();

    if (!empty($social_links)) {
      $variables['social_links'] = $social_links;
    }
  }
}

/**
 * Implements hook_theme_suggestions_field_alter().
 */
function nicsdru_corplite_theme_theme_suggestions_field_alter(array &$suggestions, array $variables) {
  // Add view mode to node field theme suggestions.
  $element = $variables['element'];

  $suggestions[] = 'field__node__' .
    $element['#field_name'] . '__' .
    $element['#view_mode'];

  $suggestions[] = 'field__node__' .
    $element['#field_name'] . '__' .
    $element['#view_mode'] . '__' .
    $element['#bundle'];

  // Force publication taxonomy reference fields to use
  // site topics template.
  $publication_taxonomy_fields = [
    'field_site_topics',
    'field_research_support',
    'field_publication_type',
    'field_site_global_topics',
  ];

  foreach ($publication_taxonomy_fields as $publication_taxonomy_field) {
    if ($variables['element']['#field_name'] === $publication_taxonomy_field) {
      $suggestions = ['field__node__field_site_topics'];
    }
  }
}

/**
 * Implements hook_preprocess_views_view().
 */
function nicsdru_corplite_theme_preprocess_views_view(&$variables) {
  $variables['attributes']['role'] = 'presentation';

  if ($variables['id'] === 'search') {
    $variables['exposed']['#attributes']['class'][] = Html::cleanCssIdentifier('search-form');
    $variables['exposed']['#attributes']['class'][] = Html::cleanCssIdentifier('search-form--site');
    $variables['exposed']['#attributes']['role'] = 'search';
    $variables['exposed']['#attributes']['aria-roledescription'] = t('site search');
    // If a label for the search input is not defined in the view, set
    // default label text.
    $search_label =& $variables['exposed']['#info']['filter-search_api_fulltext']['label'];
    if (isset($search_label) && empty($search_label)) {
      $search_label = t("Search this site");
    }
    // The label should be visually hidden.
    $variables['exposed']['query']['#title_display'] = 'invisible';
  }
}

/**
 * Implements template_preprocess_input().
 */
function nicsdru_corplite_theme_preprocess_input(&$variables) {
  // Set up configurable autocomplete message.
  $type_html = $variables['attributes']['type'];
  $text_types_html = [
    'text',
    'email',
    'tel',
    'number',
    'search',
    'password',
    'date',
    'time',
    'file',
    'color',
    'datetime-local',
    'url',
    'month',
    'week',
  ];

  if (!empty($variables['element']['#autocomplete_route_name']) && in_array($type_html, $text_types_html)) {
    $variables['autocomplete_message'] = t('Checking…');
  }

  // Set the type attribute to 'search' for search form input elements.
  $is_search_page = FALSE;

  if (isset($variables['element']['#name'])) {
    if ($variables['element']['#name'] === 'search_api_fulltext' || $variables['element']['#name'] === 'search_api_views_fulltext') {
      $is_search_page = TRUE;
    }
  }

  if (isset($variables['element']['#type']) && $variables['element']['#type'] === 'search_api_autocomplete') {
    $is_search_page = TRUE;
  }

  if ($is_search_page) {
    $variables['element']['#attributes']['type'] = 'search';
    $variables['attributes']['type'] = 'search';
  }
}

/**
 * Implements template_preprocess_file_link().
 */
function nicsdru_corplite_theme_preprocess_file_link(array &$variables) {
  if (!$variables['file'] instanceof FileInterface) {
    return;
  }

  // Map file mimetypes to user friendly document types.
  $pretty_mimes = \Drupal::service('origins_media.pretty_mime_types')
    ->getMimeTypes();

  $file = $variables['file'];

  $variables['file_size'] = ByteSizeMarkup::create($file->getSize(), \Drupal::languageManager()
    ->getCurrentLanguage()
    ->getId());
  $variables['file_url'] = $file->createFileUrl();
  $file_extension = pathinfo($variables['file_url'], PATHINFO_EXTENSION);
  $variables['attributes']->addClass([
    'file-link',
    'file--ico',
    'file--ico__' . strtolower($file_extension),
  ]);

  $file_mimetype = $file->getMimeType();
  if (array_key_exists($file->getMimeType(), $pretty_mimes)) {
    $variables['file_type'] = $pretty_mimes[$file_mimetype];
  }
  else {
    $variables['file_type'] = strtoupper(preg_replace('/^application\/(\w+)$/', '\1', $file_mimetype));
  }
}

/**
 * Implements hook_form_FORM_ID_alter().
 */
function nicsdru_corplite_theme_form_user_login_form_alter(&$form, FormStateInterface $form_state, $form_id) {
  // Alter login form to add out admin theming components.
  $form['#attached']['library'][] = 'nicsdru_corplite_theme/authenticated-styles';
}

/**
 * Implements hook_form_FORM_ID_alter().
 */
function nicsdru_corplite_theme_form_user_pass_alter(&$form, FormStateInterface $form_state, $form_id = NULL) {
  $form['#attached']['library'][] = 'nicsdru_corplite_theme/authenticated-styles';
}

/**
 * Implements hook_preprocess_facets_item_list().
 */
function nicsdru_corplite_theme_preprocess_facets_item_list(array &$variables) {
  if (!empty($variables["facet"])) {
    if (!empty($variables["facet"]->getWidget()) && $variables["facet"]->getWidget()["type"] === 'links') {
      foreach ($variables["items"] as $key => $item) {
        if (array_key_exists('class', $item["value"]["#attributes"])) {
          $variables["items"][$key]["value"]["#attributes"]["class"][] = 'facet-item--link';
        }
        else {
          $variables["items"][$key]["value"]["#attributes"]["class"] = ['facet-item--link'];
        }
      }
    }
  }
}

/**
 * Utility function to return theme config as array of Link elements.
 *
 * @return array
 *   List of social media provider links.
 */
function _nicsdru_corplite_social_links() {
  $platforms = [
    'twitter' => t('Twitter'),
    'facebook' => t('Facebook'),
    'youtube' => t('YouTube'),
    'instagram' => t('Instagram'),
    'linkedin' => t('LinkedIn'),
    'rss' => t('RSS'),
  ];
  $social_links = [];

  // Fetch links from the theme settings and use as part of render elements.
  foreach ($platforms as $id => $label) {
    $platform_url = theme_get_setting("social_links_$id", 'nicsdru_corplite_theme');

    if (empty($platform_url)) {
      // Skip any blank values.
      continue;
    }

    // Check for any internal paths.
    if (0 !== strpos($platform_url, "http")) {
      // Strip leading / if present.
      $platform_url = ltrim($platform_url, '/');
      $platform_url = 'internal:/' . $platform_url;
    }

    $social_links[$id] = Link::fromTextAndUrl($label,
      Url::fromUri($platform_url))->toRenderable();
  }

  return $social_links;
}

/**
 * Implements hook_preprocess_media().
 */
function nicsdru_corplite_theme_preprocess_media(&$variables) {
  if (empty($variables['media'])
    || ($variables['media']->bundle() !== 'document' && $variables['media']->bundle() !== 'secure_file')) {
    return;
  }

  if (!empty($variables['content']['field_media_file']) || !empty($variables['content']['field_media_file_1']) || !empty($variables['content']['field_media_file_2'])) {
    // Map file mimetypes to user friendly document types.
    $pretty_mimes = \Drupal::service('origins_media.pretty_mime_types')
      ->getMimeTypes();

    $media_fields = [
      'field_media_file',
      'field_media_file_1',
      'field_media_file_2',
    ];

    foreach ($media_fields as $media_field) {
      if (!empty($variables['content'][$media_field])) {
        $file = &$variables['content'][$media_field][0]['#file'];
      }
    }

    if (!empty($file)) {
      $variables['file_size'] = ByteSizeMarkup::create($file->getSize(), \Drupal::languageManager()
        ->getCurrentLanguage()
        ->getId());
      $variables['file_url'] = $file->createFileUrl();
      $variables['file_extension'] = pathinfo($variables['file_url'], PATHINFO_EXTENSION);
      $file_mimetype = $file->getMimeType();
      if (array_key_exists($file->getMimeType(), $pretty_mimes)) {
        $variables['file_type'] = $pretty_mimes[$file_mimetype];
      }
      else {
        $variables['file_type'] = strtoupper(preg_replace('/^application\/(\w+)$/', '\1', $file_mimetype));
      }
    }

    $variables['media_langcode'] = $variables['media']->language()->getId();
    // Do not output the langcode if is undefined.
    if ($variables['media_langcode'] === 'und') {
      unset($variables['media_langcode']);
    }
  }
}

/**
 * Implements hook_preprocess_HOOK().
 */
function nicsdru_corplite_theme_preprocess_field(&$variables) {
  if ($variables['field_name'] == 'field_attachment' || $variables['field_name'] == 'field_secure_attachment') {
    $path = \Drupal::service('path_alias.manager')
      ->getPathByAlias('/help-viewing-documents');
    if (preg_match('/node\/(\d+)/', $path, $matches)) {
      $node = Node::load($matches[1]);
      $variables['node_id'] = $node->id();
    }
  }

  // Add a class to the anchor tag on application link fields & append the icon.
  if (isset($variables['element'])
    && $variables['element']['#field_name'] === 'field_link'
    && $variables['element']['#bundle'] === 'application'
  ) {
    foreach ($variables['items'] as $key => $item) {
      $icon = '<svg aria-hidden="true" class="ico ico-arrow-right"><use xlink:href="#arrow"></use></svg>';
      $title = $item['content']['#title'];
      $classes = ['call-to-action'];

      $formattedTitle = Markup::create($title . $icon);

      $variables['items'][0]['content']['#title'] = $formattedTitle;
      $variables['items'][0]['content']['#url']->setOption('attributes', ['class' => $classes]);
    }
  }
}

/**
 * Implements hook_theme_suggestions_HOOK_alter().
 */
function nicsdru_corplite_theme_theme_suggestions_views_view_unformatted_alter(array &$suggestions, array $variables) {
  if (!empty($variables['view']->element['#display_id'])) {
    $suggestions[] = 'views_view_unformatted__' . $variables['view']->element['#display_id'];
  }
}

/**
 * Implements hook_theme_suggestions_HOOK_alter().
 */
function nicsdru_corplite_theme_theme_suggestions_taxonomy_term_alter(array &$suggestions, array $variables) {
  /** @var \Drupal\taxonomy\TermInterface $term */
  $term = $variables['elements']['#taxonomy_term'];
  $sanitized_view_mode = strtr($variables['elements']['#view_mode'], '.', '_');

  $suggestions[] = 'taxonomy_term__' . $sanitized_view_mode;
  $suggestions[] = 'taxonomy_term__' . $term->bundle();
  $suggestions[] = 'taxonomy_term__' . $term->bundle() . '__' . $sanitized_view_mode;
  $suggestions[] = 'taxonomy_term__' . $term->id();
  $suggestions[] = 'taxonomy_term__' . $term->id() . '__' . $sanitized_view_mode;

  return $suggestions;
}

/**
 * Implements hook_preprocess_region().
 */
function nicsdru_corplite_theme_preprocess_region(&$variables) {
  // Make the title field text available in the related region.
  if ($variables['region'] == 'related') {
    // Get the term id from the current path.
    $current_path = \Drupal::service('path.current')->getPath();
    $split_path = pathinfo($current_path);
    $tid = $split_path['filename'];
    if (str_contains($split_path['dirname'], 'term') && is_numeric($tid)) {
      $term = Term::load($tid);
      // Set the variable as the term label.
      $variables['topic_title'] = $term->label();
    }
  }
}

/**
 * Implements hook_preprocess_form().
 */
function nicsdru_corplite_theme_preprocess_form(array &$variables) {
  $view = $variables['element']['#id'];
  switch ($view) {
    case "views-exposed-form-search-site-search":
    case "views-exposed-form-publication-search-publications-search":
    case "views-exposed-form-consultation-search-consultations-search":
    case "views-exposed-form-news-search-news-search":
    case "views-exposed-form-alerts-search-alerts-search":
      $variables['attributes']['class'][] = Html::cleanCssIdentifier('search-form');
      $variables['attributes']['class'][] = Html::cleanCssIdentifier('search-form--page');
      break;
  }
}
