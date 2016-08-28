<?php

namespace Drupal\transmission_client\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Torrent tracker entity.
 *
 * @ConfigEntityType(
 *   id = "torrent_tracker",
 *   label = @Translation("Torrent tracker"),
 *   handlers = {
 *     "list_builder" = "Drupal\transmission_client\TorrentTrackerListBuilder",
 *     "form" = {
 *       "add" = "Drupal\transmission_client\Form\TorrentTrackerForm",
 *       "edit" = "Drupal\transmission_client\Form\TorrentTrackerForm",
 *       "delete" = "Drupal\transmission_client\Form\TorrentTrackerDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\transmission_client\TorrentTrackerHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "torrent_tracker",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/transmission/tracker/{torrent_tracker}",
 *     "add-form" = "/admin/structure/transmission/tracker/add",
 *     "edit-form" = "/admin/structure/transmission/tracker/{torrent_tracker}/edit",
 *     "delete-form" = "/admin/structure/transmission/tracker/{torrent_tracker}/delete",
 *     "collection" = "/admin/structure/transmission/tracker"
 *   }
 * )
 */
class TorrentTracker extends ConfigEntityBase implements TorrentTrackerInterface {

  /**
   * The Torrent tracker ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Torrent tracker name.
   *
   * @var string
   */
  protected $label;

  /**
   * @var bool
   */
  protected $is_active;

  /**
   * @var bool
   */
  protected $is_allowed;

  /**
   * @var bool
   */
  protected $is_private;

  /**
   * @var string
   */
  protected $url;

  /**
   * @var string
   */
  protected $announce_url;
}
