<?php
namespace Drupal\corplite_inspector_generate\Plugin\DevelGenerate;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\UriItem;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\devel_generate\DevelGenerateBase;
use Drupal\image\Plugin\Field\FieldType\ImageItem;
use Drupal\kebrina\Entity\Cadeau;
use Drupal\corplite_district_inspectors\Entity\Inspector;
use Drupal\text\Plugin\Field\FieldType\TextLongItem;

/**
 * Provides a ContentDevelGenerate plugin.
 *
 * @DevelGenerate(
 *   id = "inspector",
 *   label = @Translation("Inspector"),
 *   description = @Translation("Generate Inspectors"),
 *   url = "inspector",
 *   permission = "administer devel_generate",
 *   settings = {
 *     "num" = 50,
 *     "kill" = FALSE,
 *   }
 * )
 */
class InspectorDevelGenerate extends DevelGenerateBase {

  /**
   * Constructs a new UserDevelGenerate object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   */
  public function __construct(
    array $configuration,
          $plugin_id,
          $plugin_definition
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  public function settingsForm(array $form, FormStateInterface $form_state): array {

    $form['num'] = [
      '#type' => 'textfield',
      '#title' => $this->t('How many inspectors would you liek to generate ?'),
      '#default_value' => $this->getSetting('num'),
      '#size' => 10,
    ];

    $form['kill'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Delete existing inspectors before generation ?'),
      '#default_value' => $this->getSetting('kill'),
    ];

    return $form;
  }
  protected function generateElements(array $values): void {
    $num = $values['num'];
    $kill = $values['kill'];

    if ($kill) {
      $nbDeletedItems = 0;
      // Suppression des contenus existants
      foreach (Inspector::loadMultiple() as $inspector) {
        $inspector->delete();
        $nbDeletedItems++;
      }
      $this->setMessage($this->formatPlural($nbDeletedItems, '1 inspector has been deleted', '@count inspectors deleted'));
    }

    $definition_update_manager = \Drupal::entityDefinitionUpdateManager();
    $entity_type = $definition_update_manager->getEntityType('inspector');
    for ($i = 1; $i <= $num; $i++) {
      // Création de l'entité avec des valeurs générée aléatoirement en fonction de leur type
      // ImageItem, TextLongItem, UriItem sont a adapter en fonction des types de vos propriétés
      $inspector = Inspector::create([
        'first_name' => $this->getRandom()->word(mt_rand(2, 20)),
        'last_name' => $this->getRandom()->word(mt_rand(2, 20))
      ]);
      // On renseigne aussi les champs s'il y'en a.
      $this->populateFields($inspector);
      // Et on sauvegarde notre contenu
      $inspector->save();
    }
  }

  public function validateDrushParams(array $args, array $options = []): array {
    return [
      'num' => $options['num'],
      'kill' => $options['kill'],
    ];
  }
}
