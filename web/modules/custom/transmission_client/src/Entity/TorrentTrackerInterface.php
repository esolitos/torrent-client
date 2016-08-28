<?php

namespace Drupal\transmission_client\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Torrent tracker entities.
 */
interface TorrentTrackerInterface extends ConfigEntityInterface {

  public function isPrivate();

  public function setPrivate($is_private = TRUE);

  public function isAllowed();

  public function setAllowed($is_allowed = TRUE);

  public function isActive();

  public function setActive($is_active = TRUE);

  public function getUrl();

  public function setUrl($url = '');

  public function getAnnounce();

  public function setAnnounce($url = '');

}
