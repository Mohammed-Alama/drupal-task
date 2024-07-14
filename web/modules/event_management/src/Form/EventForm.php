<?php

declare(strict_types=1);

namespace Drupal\event_management\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the event entity edit forms.
 */
final class EventForm extends ContentEntityForm
{
  const FIFE_DAYS = 5 * 86400;

  /**
   * @throws \Exception
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    parent::validateForm($form, $form_state);

    $values = $form_state->getValues();

    $start = $values['start_date_time'][0]['value'];
    $end = $values['end_date_time'][0]['value'];

    if ($start > $end) {
      $form_state->setErrorByName('start_date_time', t('Start date should be before end date.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state): int
  {
    $result = parent::save($form, $form_state);

    $message_args = ['%label' => $this->entity->toLink()->toString()];
    $logger_args = [
      '%label' => $this->entity->label(),
      'link' => $this->entity->toLink($this->t('View'))->toString(),
    ];

    switch ($result) {
      case SAVED_NEW:
        $this->messenger()->addStatus($this->t('New event %label has been created.', $message_args));
        $this->logger('event_management')->notice('New event %label has been created.', $logger_args);
        break;

      case SAVED_UPDATED:
        $this->messenger()->addStatus($this->t('The event %label has been updated.', $message_args));
        $this->logger('event_management')->notice('The event %label has been updated.', $logger_args);
        break;

      default:
        throw new \LogicException('Could not save the entity.');
    }

    $form_state->setRedirectUrl($this->entity->toUrl());

    return $result;
  }

  /**
   * Allowed 5 days less with min value to allow creating past events
   */
  public function form(array $form, FormStateInterface $form_state): array
  {
    $form = parent::form($form, $form_state);

    $form['start_date_time']['widget'][0]['value']['#attributes']['min'] = date('Y-m-d', \Drupal::time()->getRequestTime() - self::FIFE_DAYS);
    $form['end_date_time']['widget'][0]['value']['#attributes']['min'] = date('Y-m-d', \Drupal::time()->getRequestTime() - self::FIFE_DAYS);

    return $form;
  }

}
