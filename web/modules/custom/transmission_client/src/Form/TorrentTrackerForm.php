<?php

namespace Drupal\transmission_client\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class TorrentTrackerForm.
 *
 * @package Drupal\transmission_client\Form
 */
class TorrentTrackerForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $torrent_tracker = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $torrent_tracker->label(),
      '#description' => $this->t("Label for the Torrent tracker."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $torrent_tracker->id(),
      '#machine_name' => [
        'exists' => '\Drupal\transmission_client\Entity\TorrentTracker::load',
      ],
      '#disabled' => !$torrent_tracker->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $torrent_tracker = $this->entity;
    $status = $torrent_tracker->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Torrent tracker.', [
          '%label' => $torrent_tracker->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Torrent tracker.', [
          '%label' => $torrent_tracker->label(),
        ]));
    }
    $form_state->setRedirectUrl($torrent_tracker->urlInfo('collection'));
  }

}
