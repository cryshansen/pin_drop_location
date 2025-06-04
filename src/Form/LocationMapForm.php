<?php
/**
 * Works "Location submitted: Latitude 40.650002, Longitude -73.949997" Brooklyn New York
 * get a lat/long andd present a map after submission
 *
 */
namespace Drupal\pin_drop_location\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a form to collect latitude and longitude as text fields.
 */
class LocationMapForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'pin_drop_location_location_map_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Latitude field.
    $form['latitude'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Latitude'),
      '#description' => $this->t('Enter the latitude value.'),
      '#default_value' => $form_state->getValue('latitude', ''),
      '#required' => TRUE,
    ];

    // Longitude field.
    $form['longitude'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Longitude'),
      '#description' => $this->t('Enter the longitude value.'),
      '#default_value' => $form_state->getValue('longitude', ''),
      '#required' => TRUE,
    ];

    // Add the map container if coordinates exist.
    if ($form_state->getValue('latitude') && $form_state->getValue('longitude')) {
      $form['map'] = [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#attributes' => [
          'id' => 'google-map',
          'style' => 'width: 100%; height: 400px;',
        ],
        '#attached' => [
          'library' => [
            'pin_drop_location/google_maps',
          ],
          'drupalSettings' => [
            'custom_location' => [
              'latitude' => $form_state->getValue('latitude'),
              'longitude' => $form_state->getValue('longitude'),
            ],
          ],
        ],
      ];
    }
    // Attach Bootstrap 5 and custom JavaScript to trigger the modal on page load.
    $form['#attached'] = [
      'library' => [
        'pin_drop_location/bootstrap_and_modal', // Library for Bootstrap and custom modal JavaScript.
      ],
    ];
    $form['#theme'] = 'location_map_form';
    // Submit button.
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#button_type' => 'primary',
    ];






    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $latitude = $form_state->getValue('latitude');
    $longitude = $form_state->getValue('longitude');

    if (!is_numeric($latitude) || $latitude < -90 || $latitude > 90) {
      $form_state->setErrorByName('latitude', $this->t('Please enter a valid latitude between -90 and 90.'));
    }

    if (!is_numeric($longitude) || $longitude < -180 || $longitude > 180) {
      $form_state->setErrorByName('longitude', $this->t('Please enter a valid longitude between -180 and 180.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->setRebuild(TRUE);
    $this->messenger()->addMessage($this->t('Location submitted: Latitude @lat, Longitude @lng', [
      '@lat' => $form_state->getValue('latitude'),
      '@lng' => $form_state->getValue('longitude'),
    ]));
  }

}
