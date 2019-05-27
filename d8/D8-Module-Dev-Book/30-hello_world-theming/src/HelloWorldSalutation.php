<?php

namespace Drupal\hello_world;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Prepares the salutation to the world.
 */
class HelloWorldSalutation {

  use StringTranslationTrait;

  /**
   * @var ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * HelloWorldSalutation constructor.
   * @param ConfigFactoryInterface $config_factory
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
   */

  public function __construct(ConfigFactoryInterface $config_factory,
EventDispatcherInterface $eventDispatcher) {
    $this->configFactory = $config_factory;
    $this->eventDispatcher = $eventDispatcher;
  }

  /**
   * Returns the salutation
   */
  public function getSalutation() {

    $config = $this->configFactory->get('hello_world.custom_salutation');
    $salutation = $config->get('salutation');
    if ($salutation != ""){
      $event = new SalutationEvent();
      $event->setValue($salutation);
      $event = $this->eventDispatcher->dispatch(SalutationEvent::EVENT,$event);
      return $event->getValue();
    }
    return $this->t('Hello');

  }

  /**
   * Returns a the Salutation render array.
   */
  public function getSalutationComponent() {
    //$this->killSwitch->trigger();
    $render = [
      '#theme' => 'hello_world_salutation',
      //'#salutation' => [
      //  '#contextual_links' => [
      //    'hello_world' => [
      //      'route_parameters' => []
      //    ],
      //  ]
      //],
      //'#cache' => [
      //  'max-age' => 0
      //]
    ];
    $config = $this->configFactory->get('hello_world.custom_salutation');
    //$render['#cache']['tags'] = $config->getCacheTags();
    $salutation = $config->get('salutation');
    if ($salutation != "") {
      //$render['#salutation']['#markup'] = $salutation;
      $render['#salutation'] = $salutation;
      $render['#overridden'] = TRUE;
      return $render;
    }
    $time = new \DateTime();
    $render['#target'] = $this->t('world');
    //$render['#attached'] = [
    //  'library' => [
    //    'hello_world/hello_world_clock'
    //  ]
    //];
    if ((int) $time->format('G') >= 06 && (int) $time->format('G') < 12) {
      $render['#salutation']['#markup'] = $this->t('Good morning');
      return $render;
    }
    if ((int) $time->format('G') >= 12 && (int) $time->format('G') < 18) {
      $render['#salutation']['#markup'] = $this->t('Good afternoon');
      $render['#attached']['drupalSettings']['hello_world']['hello_world_clock']['afternoon'] = TRUE;
      return $render;
    }
    if ((int) $time->format('G') >= 18) {
      $render['#salutation']['#markup'] = $this->t('Good evening');
      return $render;
    }
  }

}
