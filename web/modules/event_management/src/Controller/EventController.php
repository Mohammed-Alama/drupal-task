<?php

namespace Drupal\event_management\Controller;

use Drupal;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EventController extends ControllerBase
{
  protected $entityTypeManager;
  protected $configFactory;

  public function __construct(EntityTypeManagerInterface $entityTypeManager, ConfigFactoryInterface $configFactory)
  {
    $this->entityTypeManager = $entityTypeManager;
    $this->configFactory = $configFactory;
  }

  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('config.factory')
    );
  }

  /**
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function list(): array
  {
    $event_limit = $this->configFactory->get('event_management.settings')->get('number_of_events');
    $show_past_events = $this->configFactory->get('event_management.settings')->get('show_past_events');
    $storage = $this->entityTypeManager->getStorage('event');
    $query = $storage->getQuery();

    if (!$show_past_events) {
      $query->condition(
        'start_date_time',
        date('Y-m-d\TH:i:s', Drupal::time()->getCurrentTime()),
        '>');
    }

    $query
      ->range(0, (int)$event_limit)
      ->sort('created', 'DESC')
      ->accessCheck();

    $event_ids = $query->execute();
    $events = $storage->loadMultiple($event_ids);

    return [
      '#theme' => 'events',
      '#events' => $events,
    ];
  }

  /**
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function view($event): array
  {
    $storage = $this->entityTypeManager->getStorage('event');
    $event = $storage->load($event);

    return [
      '#theme' => 'event',
      '#event' => $event,
    ];
  }

  /**
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getTitle($event): string
  {
    $storage = $this->entityTypeManager->getStorage('event');
    /** @var \Drupal\event_management\Entity\Event $event */
    $event = $storage->load($event);

    return $event->get('title')->getString();
  }
}
