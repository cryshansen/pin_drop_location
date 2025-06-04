<?php
/**
 * This form works as expected and uses fallbacks for map lat long generation without access to geolocations
 */
namespace Drupal\pin_drop_location\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class PinDropLocationConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['pin_drop_location.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'pin_drop_location_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('pin_drop_location.settings');
    $default_lat = $config->get('default_lat');
    $default_lng = $config->get('default_long'); //change the name the field and get_server_location() fires also works which is just the server that hosts the site. 

    $default_ip=$this->get_server_location();
    // If latitude and longitude are not set in configuration, fetch from ip-api. fall back TODO may be removed later
    if (empty($default_lat) ) {
      //$fall_back_coords = get_server_location();
      $default_lat = $default_ip['latitude'];
    }
    if(empty($default_lng)){
      $default_lng = $default_ip['longitude'];
    }
    $form['api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Google Maps API Key'),
      '#default_value' => $config->get('api_key'), 
      '#description' => $this->t('Enter your Google Maps API key.'),
      '#required' => TRUE,
    ];
    $form['default_lat'] =[
      '#type' => 'textfield',
      '#title' => $this->t('A default latitude'),
      '#default_value' => $default_lat, 
      '#description' => $this->t('Enter your prefered default longitude.'),
      '#required' => TRUE,
    ];
    $form['default_long']=[
      '#type' => 'textfield',
      '#title' => $this->t('A default longitude'),
      '#default_value' => $default_lng,
      '#description' => $this->t('Enter your prefered default longitude.'),
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->configFactory->getEditable('pin_drop_location.settings')
      ->set('api_key', $form_state->getValue('api_key'))
      ->set('default_lat', $form_state->getValue('default_lat'))
      ->set('default_long', $form_state->getValue('default_long'))
      ->save();

    parent::submitForm($form, $form_state);
  }


  function get_server_location() {
    $url = 'http://ip-api.com/json/';
    
    try {
      //$response = file_get_contents($url);
      //$location_data = json_decode($response, TRUE);
      $client = \Drupal::httpClient();
      $response = $client->get($url);
      $location_data = json_decode($response->getBody()->getContents(), TRUE);

      if ($location_data && $location_data['status'] === 'success') {
        $default_lat = $location_data['lat'];
        $default_lng = $location_data['lon'];
        \Drupal::logger('pin_drop_location')->info('fetch geolocation: @message', [
        '@message' => print_r($location_data, TRUE),
      ]);
      /*
        fetch geolocation: Array ( [status] => success [country] => United States 
        [countryCode] => US [region] => NY [regionName] => New York [city] => Buffalo [zip] => 14202 
        [lat] => 42.893 [lon] => -78.8753 [timezone] => America/New_York 
        [isp] => HostPapa [org] => HostPapa [as] => AS11989 HostPapa 
        [query] => 66.84.6.26 )
      */



        return [
          'latitude' => $location_data['lat'],
          'longitude' => $location_data['lon'],
        ];
      }
      
    }catch (RequestException $e) {
      // Log error if the API call fails.
      \Drupal::logger('pin_drop_location')->error('Failed to fetch geolocation: @message', [
        '@message' => $e->getMessage(),
      ]);
      // Fallback coordinates.
      return [
        'latitude' => -25.344, //australia
        'longitude' => 131.031, //australia
      ];
    }
    
  
    // Fallback location if API fails.
    return [
      'latitude' =>  42.893, ////doesnt translate to new york  //40.7128,  40.7128° N, 74.0060° W goes to kazikstan?
      'longitude' => -78.8753 , // 74.0060, //montreal
    ];
  }
  




}
