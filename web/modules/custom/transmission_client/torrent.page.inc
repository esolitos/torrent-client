<?php

/**
 * @file
 * Contains torrent.page.inc.
 *
 * Page callback for Torrent entities.
 */

use Drupal\Core\Render\Element;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Prepares variables for Torrent templates.
 *
 * Default template: torrent.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_torrent(array &$variables) {
  // Fetch Torrent Entity Object.
  $torrent = $variables['elements']['#torrent'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
