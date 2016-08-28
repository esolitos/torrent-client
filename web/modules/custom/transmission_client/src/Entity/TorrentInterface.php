<?php

namespace Drupal\transmission_client\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Torrent entities.
 *
 * @ingroup transmission_client
 */
interface TorrentInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Torrent name.
   *
   * @return string
   *   Name of the Torrent.
   */
  public function getName();

  /**
   * Sets the Torrent name.
   *
   * @param string $name
   *   The Torrent name.
   *
   * @return \Drupal\transmission_client\Entity\TorrentInterface
   *   The called Torrent entity.
   */
  public function setName($name);

  /**
   * Gets the Torrent creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Torrent.
   */
  public function getCreatedTime();

  /**
   * Sets the Torrent creation timestamp.
   *
   * @param int $timestamp
   *   The Torrent creation timestamp.
   *
   * @return \Drupal\transmission_client\Entity\TorrentInterface
   *   The called Torrent entity.
   */
  public function setCreatedTime($timestamp);

}
