<?php

namespace Drupal\inline_editing\Plugin\rest\resource;

use Drupal\Core\Url;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;

/**
 * Returns the node ID of a given path.
 *
 * @RestResource(
 *   id = "inline_editing_nid",
 *   label = @Translation("Inline Editing: NID Resource"),
 *   uri_paths = {
 *     "canonical" = "/inline-editing/nid"
 *   }
 * )
 */
class NidResource extends ResourceBase {

  public function get() {
    if (!\Drupal::currentUser()->isAuthenticated()) {
      return new ResourceResponse([]);
    }

    $path = \Drupal::request()->get('path') ?: '/';

    $path_without_query = explode('?', $path)[0];
    $path = trim($path_without_query, '/');

    $requested_langcode = \Drupal::languageManager()->getDefaultLanguage()->getId();

    // Check in what language the path is in to determine the requested language.
    $prefixes = \Drupal::config('language.negotiation')->get('url.prefixes');
    $requested_langcode_prefix = '';
    if ($prefixes) {
      foreach ($prefixes as $langcode => $prefix) {
        if ($prefix !== NULL) {
          if (substr($path, 0, 2) === $prefix) {
            $path = trim(ltrim($path, $prefix), '/');
            $requested_langcode = $langcode;
            $requested_langcode_prefix = '/' . $prefix;
            break;
          }
        }
      }
    }

    /** @var \Drupal\path_alias\AliasManagerInterface $aliasManager */
    $aliasManager = \Drupal::service('path_alias.manager');

    // Get the node path so an URL can be build.
    if ($path === '') {
      // Get home page from config.
      $node_path = \Drupal::config('system.site')->get('page.front');
    }
    else {
      // Else get node path through alias resolver.
      $node_path = $aliasManager->getPathByAlias('/' . $path, $requested_langcode);
    }

    $url = Url::fromUri("internal:" . $node_path);
    if ($url->isRouted()) {
      $params = Url::fromUri("internal:" . $node_path)->getRouteParameters();
    }

    $response = new ResourceResponse([
      'nid' => isset($params['node']) ? $params['node'] : NULL,
      'prefix' => $requested_langcode_prefix,
      'isFound' => isset($params['node']),
    ]);

    $response->addCacheableDependency('url.query_args');

    return $response;
  }

}
