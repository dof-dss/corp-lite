<?php

/**
 * @file
 * Theme hook_alter functions.
 */

use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_system_theme_settings_alter().
 */
function nicsdru_corplite_theme_form_system_theme_settings_alter(&$form, FormStateInterface $form_state, $form_id = NULL)
{
// Work-around for a core bug affecting admin themes. See issue #943212.
  if (isset($form_id)) {
    return;
  }

// Add a theme settings group to gather social media link URLs.
  $form['social_links'] = [
    '#type' => 'details',
    '#title' => t('Social media links'),
    '#open' => TRUE,
    '#description' => t('Add social media platform details here.'),
  ];
  $form['social_links']['social_links_twitter'] = [
    '#type' => 'textfield',
    '#title' => t('Twitter / X'),
    '#default_value' => theme_get_setting('social_links_twitter'),
    '#placeholder' => 'Enter a URL, such as https://twitter.com/yourorg',
  ];
  $form['social_links']['social_links_facebook'] = [
    '#type' => 'textfield',
    '#title' => t('Facebook'),
    '#default_value' => theme_get_setting('social_links_facebook'),
    '#placeholder' => 'Enter a URL, such as https://facebook.com/yourorg',
  ];
  $form['social_links']['social_links_youtube'] = [
    '#type' => 'textfield',
    '#title' => t('YouTube'),
    '#default_value' => theme_get_setting('social_links_youtube'),
    '#placeholder' => 'Enter a URL, such as https://youtube.com/yourorg',
  ];
  $form['social_links']['social_links_instagram'] = [
    '#type' => 'textfield',
    '#title' => t('YouTube'),
    '#default_value' => theme_get_setting('social_links_instagram'),
    '#placeholder' => 'Enter a URL, such as https://www.instagram.com/yourorg',
  ];
  $form['social_links']['social_links_linkedin'] = [
    '#type' => 'textfield',
    '#title' => t('Linkedin'),
    '#default_value' => theme_get_setting('social_links_linkedin'),
    '#placeholder' => 'Enter a URL, such as https://uk.linkedin.com/company/yourorg',
  ];
  $form['social_links']['social_links_rss'] = [
    '#type' => 'textfield',
    '#title' => t('RSS'),
    '#default_value' => theme_get_setting('social_links_rss'),
    '#placeholder' => 'Enter a URL, such as https://example.com/rss.xml',
  ];
}

/**
 * Implements hook_form_BASE_FORM_ID_alter().
 */
function nicsdru_corplite_theme_form_node_preview_form_select_alter(&$form, FormStateInterface $form_state, $form_id) {
  // Additional styles to the node preview form.
  unset($form['view_mode']);
  $form['backlink']['#options']['attributes']['class'] += [
    'button',
    'btn',
    'call-to-action',
    'btn-left'
  ];
}
