<?php

namespace Drupal\event_management\Plugin\Block;

use Drupal;
use Drupal\Core\Block\Annotation\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Link;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'LatestEventsBlock' block.
 *Ω
 * @Block(
 *  id = "latest_events",
 *  admin_label = @Translation("Latest events"),
 * )
 */
class LatestEventsBlock extends BlockBase implements ContainerFactoryPluginInterface
{
  /**
   * @var \Drupal\Core\Database\Connection
   */
  protected Connection $database;
  /**
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  private ConfigFactoryInterface $configFactory;

  /**
   * Constructs a new LatestEventsBlock.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param string $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, Connection $database, ConfigFactoryInterface $configFactory)
  {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->database = $database;
    $this->configFactory = $configFactory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
  {
    /** @var Connection $database */
    $database = $container->get('database');
    /** @var ConfigFactoryInterface $config */
    $config = $container->get('config.factory');

    return new static(
      $configuration, $plugin_id, $plugin_definition,
      $database,
      $config
    );
  }

  /**
   * Returns the latest 5 events.
   *
   * @throws \Exception
   * @return array An array of event rows, each row as an associative array
   */
  protected function getLatestEvents(): array
  {
    $show_past_events = $this->configFactory->get('event_management.settings')->get('show_past_events');
    $query = $this->database->select('event', 'e')
      ->fields('e', [
        'id',
        'title',
        'created',
      ]);

    if (!$show_past_events) {
      $query->condition(
        'start_date_time',
        date('Y-m-d\TH:i:s', Drupal::time()->getCurrentTime()),
        '>');
    }

    $query
      ->orderBy('created', 'DESC')
      ->range(0, 5);

    return $query->execute()->fetchAllAssoc('id');
  }

  /**
   * @throws \Exception
   */
  public function build(): array
  {
    $events = $this->getLatestEvents();

    $build = [
      '#theme' => 'item_list',
      '#items' => [],
    ];

    foreach ($events as $event) {
      $build['#items'][] = Link::fromTextAndUrl($event->title, Url::fromUri("internal:/events/{$event->id}"));
    }

    return $build;
  }
}
