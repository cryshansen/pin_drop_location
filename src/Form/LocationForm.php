<?php
/**
 * Just collect a lat and long. will need a form that converts a city to alt and long
 */
namespace Drupal\pin_drop_location\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a form to collect latitude and longitude as text fields.
 */
class LocationForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'pin_drop_location_text_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['latitude'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Latitude'),
      '#description' => $this->t('Enter the latitude value.'),
      '#default_value' => '',
      '#required' => TRUE,
    ];

    $form['longitude'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Longitude'),
      '#description' => $this->t('Enter the longitude value.'),
      '#default_value' => '',
      '#required' => TRUE,
    ];

     $form['country'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Longitude'),
      '#description' => $this->t('Enter the longitude value.'),
      '#default_value' => 'United States',
      '#required' => TRUE,
    ];

     $form['countryCode'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Longitude'),
      '#description' => $this->t('Enter the longitude value.'),
      '#default_value' => 'US',
      '#required' => TRUE, 
     ];

     $form['region'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Longitude'),
      '#description' => $this->t('Enter the longitude value.'),
      '#default_value' => 'NY',
      '#required' => TRUE,
     ];

     $form['regionName'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Longitude'),
      '#description' => $this->t('Enter the longitude value.'),
      '#default_value' => 'New York',
      '#required' => TRUE,
     ]; 

     $form['city'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Longitude'),
      '#description' => $this->t('Enter the longitude value.'),
      '#default_value' => 'Buffalo',
      '#required' => TRUE, 
     ];

     $form['zip'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Longitude'),
      '#description' => $this->t('Enter the longitude value.'),
      '#default_value' => '14202',
      '#required' => TRUE,
      ];

     $form['lat'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Longitude'),
      '#description' => $this->t('Enter the longitude value.'),
      '#default_value' => '42.893',
      '#required' => TRUE,
     ];

     $form['lon'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Longitude'),
      '#description' => $this->t('Enter the longitude value.'),
      '#default_value' => '-78.8753',
     ];

     $form['timezone'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Longitude'),
      '#description' => $this->t('Enter the longitude value.'),
      '#default_value' => 'America/New_York',
      '#required' => TRUE,
     ];

     $form['query_ip'] =[
      '#type' => 'textfield',
      '#title' => $this->t('Longitude'),
      '#description' => $this->t('Enter the longitude value.'),
      '#default_value' => '66.84.6.26',
     ];
     
    /* TODO: Build out country code region region name city zip so can use all components in display
   
        
      */


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
    $latitude = $form_state->getValue('latitude');
    $longitude = $form_state->getValue('longitude');

    $this->messenger()->addMessage($this->t('Location saved: Latitude @lat, Longitude @lng', [
      '@lat' => $latitude,
      '@lng' => $longitude,
    ]));
  }

}