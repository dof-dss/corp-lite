<?php

namespace Drupal\etini_district_inspectors\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\etini_district_inspectors\Entity\Inspector;
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
    $form = parent::buildForm($form, $form_state);

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
      '#required' => TRUE,
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
      '#required' => TRUE,
      '#description' => $this->t($to_message)
    ];
    $form['actions']['submit']['#value'] = $this->t("Transfer Schools");
    $msg = t("Are you sure that you want to transfer these schools ?");
    $form['#attributes'] = array('onsubmit' => 'if(confirm("' . $msg . '")) return true; return false;');

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Get inspector details
    $from_id = $form_state->getValue('old_inspector_id');
    $from_inspector = Inspector::Load($from_id);
    $from_inspector_name = '';
    if (!empty($from_inspector)) {
      $from_inspector_name = $from_inspector->get("name")->getString();
    }
    $to_id = $form_state->getValue('new_inspector_id');
    $to_inspector = Inspector::Load($to_id);
    $to_inspector_name = '';
    if (!empty($to_inspector)) {
      $to_inspector_name = $to_inspector->get("name")->getString();
    }

    // Get a list of schools attached to the old inspector.
    $storage = $this->entityTypeManager->getStorage('school');
    $ids = $storage->getQuery()
      ->accessCheck(FALSE)
      ->condition('inspector_id', $from_id)
      ->execute();
    $n = 0;
    foreach ($ids as $id) {
      $school = School::load($id);
      if ($school->get('inspector_id')->getString() == $from_id) {
        // Move school to new inspector.
        //$school->setNewRevision(TRUE);
        \Drupal::logger('etini_district_inspectors')->notice("setting new revision for school $id");
        $school->set('inspector_id', $to_id);
        $revision = $storage->createRevision($school);
        $revision->setRevisionCreationTime(\Drupal::time()->getCurrentTime());
        $revision->setRevisionUserId(\Drupal::currentuser()->id());
        $revision->save();
        $n++;
      }
    }
    $message = "Moved $n schools from inspector '$from_inspector_name' ($from_id), to inspector '$to_inspector_name' ($to_id)";
    \Drupal::logger('etini_district_inspectors')->notice(t($message));
    $this->messenger()->deleteByType('status');
    $this->messenger()->addStatus($message);
  }
}
