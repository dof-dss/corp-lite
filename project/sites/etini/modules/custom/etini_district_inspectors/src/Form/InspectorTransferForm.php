<?php

namespace Drupal\etini_district_inspectors\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\etini_district_inspectors\Entity\School;
use Drupal\media\IFrameUrlHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Implements admin form to allow transfer of schools to new inspector.
 */
class InspectorTransferForm extends ConfigFormBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'inspector_transfer_form';
  }

  /**
   * MediaSettingsForm constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory service.
   * @param \Drupal\Core\Config\TypedConfigManagerInterface $typedConfigManager
   *   The typed config manager.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(ConfigFactoryInterface $config_factory, TypedConfigManagerInterface $typedConfigManager, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($config_factory, $typedConfigManager);
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('config.typed'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $from_message = "Select the District Inspector that you would like to transfer schools <b>from</b>.";

    $to_message = "Select the District Inspector that you would like to transfer schools <b>to</b>.";

    $form['old_inspector_id'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('District Inspector (old)'),
      '#target_type' => 'inspector',
      '#selection_handler' => 'views',
      '#selection_settings' => [
        'view' => [
          'view_name' => 'district_inspector_typeahead',
          'display_name' => 'inspector_reference',
          'arguments' => []
        ]
      ],
      '#description' => $this->t($from_message)
    ];

    $form['new_inspector_id'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('District Inspector (new)'),
      '#target_type' => 'inspector',
      '#selection_handler' => 'views',
      '#selection_settings' => [
        'view' => [
          'view_name' => 'district_inspector_typeahead',
          'display_name' => 'inspector_reference',
          'arguments' => []
        ]
      ],
      '#description' => $this->t($to_message)
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $from_id = $form_state->getValue('old_inspector_id');
    $to_id = $form_state->getValue('new_inspector_id');

    $message = "From is $from_id , to is $to_id";
    \Drupal::logger('etini_district_inspectors')->notice(t($message));

    //$results = $this->entityTypeManager->getStorage('school')->loadMultiple();
    $storage = \Drupal::entityTypeManager()->getStorage('school');
    $ids = $storage->getQuery()
      ->accessCheck(FALSE)
      ->condition('inspector_id', $from_id)
      ->execute();
    foreach ($ids as $id) {
      \Drupal::logger('etini_district_inspectors')->notice(t("ID is $id"));
      $school = School::load($id);
      $message = "Inspector is " . $school->get('inspector_id')->getString();
      \Drupal::logger('etini_district_inspectors')->notice(t($message));
    }

  }

}
