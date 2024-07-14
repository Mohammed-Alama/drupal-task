<?php

declare(strict_types=1);

namespace Drupal\event_management\Entity;

use Drupal\Core\Entity\Annotation\ContentEntityType;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\event_management\EventInterface;

/**
 * Defines the event entity class.
 *
 * @ContentEntityType(
 *   id = "event",
 *   label = @Translation("Event"),
 *   label_singular = @Translation("Event"),
 *   label_plural = @Translation("Events"),
 *   label_count = @PluralTranslation(
 *     singular = "@count Event",
 *     plural = "@count Events",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\event_management\EventListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "add" = "Drupal\event_management\Form\EventForm",
 *       "edit" = "Drupal\event_management\Form\EventForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *       "delete-multiple-confirm" = "Drupal\Core\Entity\Form\DeleteMultipleForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "event",
 *   admin_permission = "administer event",
 *   entity_keys = {
 *     "id" = "id",
 *     "title" = "title",
 *     "description" = "description",
 *     "start_date_time" = "start_date_time",
 *     "end_date_time" = "end_date_time",
 *     "image" = "image",
 *     "category" = "category",
 *     "created" = "created",
 *     "changed" = "changed",
 *   },
 *   links = {
 *     "collection" = "/admin/content/events",
 *     "add-form" = "/admin/content/events/add",
 *     "view" = "/events/{event}",
 *     "canonical" = "/admin/content/events/{event}",
 *     "edit-form" = "/admin/content/events/{event}/edit",
 *     "delete-form" = "/admin/content/events/{event}/delete",
 *     "delete-multiple-form" = "/admin/content/events/delete-multiple",
 *   },
 *   field_ui_base_route = "entity.event.settings",
 * )
 */
final class Event extends ContentEntityBase implements EventInterface
{

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type): array
  {

    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['description'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Description'))
      ->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => 10,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'text_default',
        'label' => 'above',
        'weight' => 10,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['category'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Category'))
      ->setDescription(t('The name of the Category.'))
      ->setSetting('target_type', 'taxonomy_term')
      ->setSetting('handler', 'default:taxonomy_term')
      ->setSetting('handler_settings',
        ['target_bundles' => ['event_category' => 'event_category']]
      )
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'entity_reference_label',
        'weight' => -3,
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => -3,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['start_date_time'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Start Date and Time'))
      ->setDescription(t('The start date and time of the Event.'))
      ->setRequired(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'datetime_default',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'datetime_default',
        'weight' => -4,
      ]);

    $fields['end_date_time'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('End Date and Time'))
      ->setDescription(t('The end date and time of the Event.'))
      ->setRequired(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'datetime_default',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'datetime_default',
        'weight' => -4,
      ]);

    $fields['image'] = BaseFieldDefinition::create('image')
      ->setLabel(t('Image'))
      ->setRequired(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'image',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'image_image',
        'weight' => 0,
      ]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the event was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the event was last edited.'));

    return $fields;
  }
}
