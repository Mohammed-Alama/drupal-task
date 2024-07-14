<?php

declare(strict_types=1);

namespace Drupal\event_management;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Provides a list controller for the event entity type.
 */
final class EventListBuilder extends EntityListBuilder
{

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array
  {
    $header['id'] = $this->t('ID');
    $header['title'] = $this->t('Title');
    $header['category'] = $this->t('Category');
    $header['image'] = $this->t('Image');
    $header['created'] = $this->t('Created');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array
  {
    /** @var \Drupal\event_management\EventInterface $entity */
    $row['id'] = $entity->id();
    $row['title'] = $entity->get('title')->getString();
    $row['category'] = $entity->get('category')->entity->getName();
    $image = $entity->get('image')->entity;
    if ($image) {
      $row['image'] = [
        'data' => [
          '#theme' => 'image_style',
          '#style_name' => 'thumbnail',
          '#uri' => $image->getFileUri(),
        ],
      ];
    }
    else {
      $row['image'] = $this->t('No image');
    }

    $row['created']['data'] = $entity->get('created')->view(['label' => 'hidden']);
    return $row + parent::buildRow($entity);
  }

}
