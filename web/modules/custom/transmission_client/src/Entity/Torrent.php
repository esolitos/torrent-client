<?php

namespace Drupal\transmission_client\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Torrent entity.
 *
 * @ingroup transmission_client
 *
 * @ContentEntityType(
 *   id = "torrent",
 *   label = @Translation("Torrent"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "views_data" = "Drupal\transmission_client\Entity\TorrentViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\transmission_client\Form\TorrentForm",
 *       "add" = "Drupal\transmission_client\Form\TorrentForm",
 *       "edit" = "Drupal\transmission_client\Form\TorrentForm",
 *       "delete" = "Drupal\transmission_client\Form\TorrentDeleteForm",
 *     },
 *     "access" = "Drupal\transmission_client\TorrentAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\transmission_client\TorrentHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "torrent",
 *   admin_permission = "administer torrent entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *   },
 *   links = {
 *     "canonical" = "/torrent/{torrent}",
 *     "add-form" = "/torrent/add",
 *     "edit-form" = "/torrent/{torrent}/edit",
 *     "delete-form" = "/torrent/{torrent}/delete",
 *   },
 *   field_ui_base_route = "torrent.settings"
 * )
 */
class Torrent extends ContentEntityBase implements TorrentInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Uploaded by'))
      ->setDescription(t('The user ID of author of the Torrent entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Torrent.'))
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the Torrent was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the Torrent was last synced with Transmission Daemon.'));

    //
    // Torrent-specific fields
    //

    $fields['torrent_status'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Torrent Status'))
      ->setDescription(t('Indicates the status of the torrent: downloading, seeding, stopped, ..'))
      ->setSettings([
        'allowed_values' => [
          'active' => t('Downloading'),
          'seeding' => t('Seeding'),
          'stopped' => t('Paused'),
          'deleted' => t('Removed'),
        ],
      ])
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'string',
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['transmission_id'] = BaseFieldDefinition::create('integer')
      ->setLabel("Transmission internal ID")
      ->setDescription(t('(Internal) The ID coming from Transmission Daemon, beware that this will change on daemon restart, Drupal cron should take care of updating it in this case, but there might be a delay.'))
      ->setSetting('unsigned', TRUE)
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['torrent_hash'] = BaseFieldDefinition::create('string')
      ->setLabel('Torrent Hash')
      ->setDescription(t('(Internal) Hash of the torrent.useful to load a specific torrent from Transmission Daemon'))
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

}
