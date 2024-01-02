<?php

namespace Drupal\burst_distribution\EventSubscriber;

use Drupal\Core\Site\Settings;
use Drupal\Core\Url;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RedirectToLoginSubscriber implements EventSubscriberInterface {

  public static function getSubscribedEvents() {
    return [
      KernelEvents::REQUEST => ['checkForRedirect']
    ];
  }

  public function checkForRedirect(GetResponseEvent $event) {
    $request = $event->getRequest();
    $route_name = \Drupal::routeMatch()->getRouteName();
    $skip_route = [
        'entity.webform.canonical',
        'user.login',
        'user.pass',
        'user.reset',
        'user.reset.form',
        'user.reset.login',
        'graphql.query.default:default',
        'simple_sitemap.sitemap',
        'simple_sitemap.sitemap_default',
        'hn.endpoint',
        'simple_sitemap.sitemap_variant',
        'simple_sitemap.sitemap_default',
        'simple_sitemap.sitemap_xsl',
        'openid_connect.redirect_controller_redirect',
      ];

    \Drupal::moduleHandler()->invokeAll('burst_distribution_alter_skip_route', [&$skip_route]);

    if (
      \Drupal::currentUser()->isAuthenticated() ||
      in_array($route_name, $skip_route) ||
      substr($route_name, 0, 5) === 'rest.' ||
      $event->getRequest()->getRequestFormat() !== 'html'
    ) {
      return;
    }

    $should_redirect_to_login = Settings::get('burst_distribution_should_redirect_to_login');
    if ($should_redirect_to_login && !$should_redirect_to_login($route_name, $request)) {
      return;
    }

    $response = new RedirectResponse(Url::fromRoute('user.login')->toString());

    // There is some bug in Hero where if we don't send the response immediately, it will redirect to '/' creating a
    // redirect loop.
    if (true) {
      $response->send();
      die;
    }

    $event->setResponse($response);
    $event->stopPropagation();
  }
}
