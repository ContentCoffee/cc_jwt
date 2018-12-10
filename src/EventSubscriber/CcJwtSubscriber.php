<?php

namespace Drupal\cc_jwt\EventSubscriber;

use Drupal\Core\Session\AccountInterface;
use Drupal\jwt\Authentication\Event\JwtAuthEvents;
use Drupal\jwt\Authentication\Event\JwtAuthGenerateEvent;
use Drupal\user\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CcJwtSubscriber implements EventSubscriberInterface {
  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  public static function getSubscribedEvents() {
    $events[JwtAuthEvents::GENERATE][] = ['setUuid', 98];
    $events[JwtAuthEvents::GENERATE][] = ['setExp', 97];
    return $events;
  }

  public function __construct(AccountInterface $user) {
    $this->currentUser = $user;
  }

  /**
   * Making sure we add the UUID in a clean way to an auth token.
   * @param \Drupal\jwt\Authentication\Event\JwtAuthGenerateEvent $event
   */
  public function setUuid(JwtAuthGenerateEvent $event) {
    $user = User::load($this->currentUser->id());
    $event->addClaim(
      ['drupal', 'uuid'],
      $user->uuid()
    );
  }

  /**
   * @param \Drupal\jwt\Authentication\Event\JwtAuthGenerateEvent $event
   */
  public function setExp(JwtAuthGenerateEvent $event) {
    $event->addClaim('exp', strtotime('+2 weeks'));
  }

}