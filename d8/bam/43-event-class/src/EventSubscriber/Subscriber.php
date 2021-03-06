<?php

namespace Drupal\blindd8\EventSubscriber;

use Drupal\blindd8\BlindD8NotableEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class Subscriber.
 */
class Subscriber implements EventSubscriberInterface {


    /**
     * Constructs a new Subscriber object.
     */
    public function __construct() {

    }

    /**
     * {@inheritdoc}
     */
    static function getSubscribedEvents() {
        $events[KernelEvents::RESPONSE] = ['kernel_response'];
        $events[KernelEvents::REQUEST][] = array('checkForRedirect');
        $events['blindd8.notable_event'][] = array('onNotableEvent');
        return $events;
    }
    /**
     * This method is called whenever the kernel.response event is
     * dispatched.
     *
     * @param Event $event
     */
    public function kernel_response(Event $event) {

        $route = \Drupal::routeMatch()->getRouteName();
        if ($route == 'system.404') {
            $response = $event->getResponse();
            $response->setContent('Blind date, get it?!');
            $response->setStatusCode('404');
        }
    }

    /**
     * Redirects the user when they're requesting our nearly blank page.
     *
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     *   The response event.
     */
    public function checkForRedirect(GetResponseEvent $event) {

        $route_name = \Drupal::routeMatch()->getRouteName();

        if ($route_name == 'blindd8.redirect') {

            $messenger = \Drupal::messenger();
            $messenger->addMessage('You have been redirected', $messenger::TYPE_WARNING);

            $url = \Drupal\Core\Url::fromUri('internal:/')->toString();
            $response = new RedirectResponse($url);
            $event->setResponse($response);

        }
    }

    public function onNotableEvent(BlindD8NotableEvent $event) {
        $event->setString('Updated string yeah yes!!!');
    }
}
