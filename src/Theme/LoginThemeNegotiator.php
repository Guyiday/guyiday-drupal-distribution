<?php

namespace Drupal\burst_distribution\Theme;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Theme\ThemeNegotiatorInterface;

/**
 * Sets the selected theme on specified pages.
 */
class LoginThemeNegotiator implements ThemeNegotiatorInterface {

  /**
   * Select specified pages for specified role and apply theme.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The current route match object.
   *
   * @return bool
   *   TRUE if this negotiator should be used or FALSE to let other negotiators
   *   decide.
   */
  public function applies(RouteMatchInterface $route_match) {
    return \Drupal::currentUser()->isAnonymous() && in_array($route_match->getRouteName(), ['user.login', 'user.pass']);
  }

  /**
   * Determine the active theme for the request.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The current route match object.
   *
   * @return string|null
   *   The name of the theme, or NULL if other negotiators, like the configured
   *   default one, should be used instead.
   */
  public function determineActiveTheme(RouteMatchInterface $route_match) {
    return 'burst_login_theme';
  }

}
