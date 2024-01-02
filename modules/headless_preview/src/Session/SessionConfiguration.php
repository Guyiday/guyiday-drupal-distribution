<?php

namespace Drupal\headless_preview\Session;

use Drupal\Core\Session\SessionConfiguration as SessionConfigurationBase;
use Symfony\Component\HttpFoundation\Request;

class SessionConfiguration extends SessionConfigurationBase {

  /**
   * {@inheritDoc}
   */
  protected function getCookieDomain(Request $request) {
    $host = $request->getHost();
    $cookie_domain = $host;

    // A cookie_domain has to start with a dot '.'.
    // The cookie_domain can span over multiple subdomains,
    // e.g.: cms.domain.com and www.domain.com has .domain.com
    // as its cookie domain.
    if (strpos($host, 'cms.') === 0) {
      $cookie_domain = str_replace('cms.', '.', $request->getHost());
    }
    elseif (strpos($host, 'redactie.') === 0) {
      $cookie_domain = str_replace('redactie.', '.', $request->getHost());
    }

    // The cookie domain can be altered using a hook.
    \Drupal::moduleHandler()->invokeAll('cookie_domain_alter', [&$cookie_domain]);

    return $cookie_domain;
  }

}
