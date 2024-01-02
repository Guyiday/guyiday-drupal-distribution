<?php

namespace Drupal\superadmin\EventSubscriber;

use Drupal\Core\Url;
use Drupal\superadmin\Controller\ChooseRolesController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RedirectEventSubscriber implements EventSubscriberInterface {

  public static function getSubscribedEvents() {
    return [
      KernelEvents::REQUEST => ['checkForRedirect']
    ];
  }

  public function checkForRedirect(GetResponseEvent $event) {
    // We only need to check for the superadmin.
    $allowed = ChooseRolesController::accessAllowed(\Drupal::currentUser())->isAllowed();
    if (!$allowed) return;

    $route = \Drupal::routeMatch()->getRouteName();

    $allowed_routes = [
      // We don't want to redirect if we're already on the page we are redirecting to.
      'superadmin.choose_roles',

      // There are some actions available in the admin toolbar when still selecting which user you want to be. These
      // routes should also be selectable.
      'system.404',
      'system.403',
      'admin_toolbar_tools.flush',
      'user.logout',

      // We also don't want to redirect when using a password-reset like functionality. For example, when using 'drush
      // uli'.
      'user.reset.login',
      'user.pass',
      'user.login',
      'entity.user.edit_form',
      'frontend_editing.edit_form',
    ];
    if (in_array($route, $allowed_routes)) {
      return;
    }

    // Don't redirect when doing GraphQL queries.
    if (strpos($route, 'graphql.query') !== FALSE) {
      return;
    }

    // If the user has chosen to be the superadmin, we aren't redirecting.
    $request = \Drupal::request();
    if ($request->hasSession() && $request->getSession()->get('chosen_superadmin')) {
      return;
    }

    $event->setResponse(new RedirectResponse(Url::fromRoute('superadmin.choose_roles')->toString()));
    $event->stopPropagation();
  }


}
