<?php

namespace Drupal\transmission_client\Entity;

use Drupal\views\EntityViewsData;
use Drupal\views\EntityViewsDataInterface;

/**
 * Provides Views data for Torrent entities.
 */
class TorrentViewsData extends EntityViewsData implements EntityViewsDataInterface {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['torrent']['table']['base'] = array(
      'field' => 'id',
      'title' => $this->t('Torrent'),
      'help' => $this->t('The Torrent ID.'),
    );

    return $data;
  }

}
