<?php

namespace Drupal\inline_editing\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\paragraphs\Entity\Paragraph;

/**
 * Defines ParagraphEditFormController controller.
 */
class ParagraphEditFormController extends ControllerBase {

  /**
   * Display the markup.
   *
   * @return array
   *   Return markup array.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function renderForm() {
    $id = \Drupal::request()->get('id');
    $uuid = \Drupal::request()->get('uuid');
    $langcode = \Drupal::request()->get('langcode');

    /** @var \Drupal\Core\Entity\EntityInterface $entity */
    $entity = NULL;
    $form_options = [];

    if ($id) {
      $entity = Paragraph::load($id);
    }

    if ($uuid) {
      $uuid = explode('--', $uuid)[0];
      /** @var \Drupal\Core\Entity\EntityRepositoryInterface $entityRepository */
      $entityRepository = \Drupal::service('entity.repository');
      $entity = $entityRepository->loadEntityByUuid('paragraph', $uuid);
    }

    if ($langcode) {
      try {
        $entity = $entity->getTranslation($langcode);
        $form_options['langcode'] = $langcode;
      }
      catch (\Exception $exception) {
        return [];
      }
    }

    /** @var \Drupal\Core\Entity\EntityFormBuilder $form_builder */
    $form_builder = \Drupal::service('entity.form_builder');

    $form = $form_builder->getForm($entity, 'edit', $form_options);

    unset($form['content_translation']);
    unset($form['actions']['delete_translation']);

    $form['#attached']['library'][] = 'inline_editing/inline_editing.form';

    return $form;
  }

}
