<?php

namespace Drupal\transmission_client\Form;

use Drupal\Core\Entity\ContentEntityDeleteForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a form for deleting Torrent entities.
 *
 * @ingroup transmission_client
 */
class TorrentDeleteForm extends ContentEntityDeleteForm {
  /**
   * @inheritDoc
   */
  public function getDescription() {
    return $this->t('Beware that the torrent will be removed and the data deleted also on the connected Transmission Daemon server. <strong>This action cannot be undone.</strong>');
  }


  /**
   * @inheritDoc
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // TODO: Connect to Transmission Daemon and delete the torrent.
    parent::submitForm($form, $form_state);
  }


}
