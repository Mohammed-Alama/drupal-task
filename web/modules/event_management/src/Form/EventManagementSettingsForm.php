<?php

namespace Drupal\event_management\Form;

use Drupal;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class EventManagementSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'event_management_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['event_management.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('event_management.settings');

    $form['show_past_events'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show past events'),
      '#default_value' => $config->get('show_past_events'),
    ];

    $form['number_of_events'] = [
      '#type' => 'number',
      '#title' => $this->t('Number of events to list'),
      '#default_value' => $config->get('number_of_events'),
      '#min' => 0,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   * @throws \Exception
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $config = $this->config('event_management.settings');
    $config
      ->set('show_past_events', $form_state->getValue('show_past_events'))
      ->set('number_of_events', $form_state->getValue('number_of_events'))
      ->save();

    Drupal::database()->insert('event_management_config_log')
      ->fields([
        'user_id' => Drupal::currentUser()->id(),
        'changed' => Drupal::time()->getRequestTime(),
        'config' => serialize($config->get()),
      ])
      ->execute();
  }
}
