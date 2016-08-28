<?php

namespace Drupal\transmission_client;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Torrent entity.
 *
 * @see \Drupal\transmission_client\Entity\Torrent.
 */
class TorrentAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\transmission_client\Entity\TorrentInterface $entity */
    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'view torrent entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit torrent entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete torrent entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add torrent entities');
  }

}
