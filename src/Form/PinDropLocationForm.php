<?php 
/** this form route uses a map twig which is hard coded
 * this form should just display the lat and long form fields that allow to add a pin.
 * The display should handle the map portion. TBD
 */
namespace Drupal\pin_drop_location\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;

class PinDropLocationForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'pin_drop_location_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Get server location. //for when a user doesnt allow  

    // Retrieve the API key from the configuration.
    $config = $this->config('pin_drop_location.settings');
    $api_key = $config->get('api_key');

      
    $form['map'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'map', 'style' => 'height: 400px; width: 100%;'],
    ];
    $form['lat'] = [
      '#type' => 'textfield',
      '#attributes' => ['id' => 'lat'],
      '#default_value' => (float) $config->get('default_lat'),
      '#attributes' => ['readonly' => 'readonly'], 
      '#id' => 'latitude',  // Set a custom ID for the field
    ];
   
    $form['lng'] = [
      '#type' => 'textfield',
      '#attributes' => ['id' => 'lng'],
      '#default_value' => (float) $config->get('default_long'), 
      '#description' => $this->t('Click on the map to set longitude.'),
      '#attributes' => ['readonly' => 'readonly'],
      '#id' => 'longitude',
    ];
    
    // Reference field (text)
    $form['reference_field'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Reference Field'),
      '#description' => $this->t('Optional field for interest or referred reason to pin. ei) good location for party.'),
      '#required' => FALSE,
    ];
    
    // Reference image (image)
    $form['image'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Upload Reference Image'),
      '#upload_location' => 'public://images/',
      '#upload_validators' => [
        'file_validate_extensions' => ['png jpg jpeg'],
        'file_validate_image_resolution' => ['1024x1024', '50x50'],
      ],
      '#required' => FALSE,
    ];

    // Attach the JavaScript library to initialize the map.
    $form['#attached']['library'][] = 'pin_drop_location/google_map';
    $form['#attached']['drupalSettings']['pin_drop_location'] = [
      'api_key' => $api_key,
      'latitude' =>  (float) $config->get('default_lat'),
      'longitude' =>  (float) $config->get('default_long'),
    ];

    

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save Location'),
    ];


    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $lat = $form_state->getValue('lat');
    $lng = $form_state->getValue('lng');
    $reference_field = $form_state->getValue('reference_field');
    $timestamp = time();
    $fid = $form_state->getValue('image')[0] ?? NULL;

    if ($fid) {
      $file = \Drupal\file\Entity\File::load($fid);
      if ($file) {
        $file->setPermanent();
        $file->save();
        \Drupal::messenger()->addMessage($this->t('Image uploaded successfully: @filename', ['@filename' => $file->getFilename()]));
      }
    }
    // Insert data into the database.
    /*$connection = Database::getConnection();
    $connection->insert('pin_drop_location')
      ->fields([
        'lat' => $lat,
        'lng' => $lng,
        'timestamp' => $timestamp,
        'reference_field' = $reference_field,  //update database field exist

      ])
      ->execute();*/
    // Insert into DB.
      $fields = [
        'lat' => $lat,
        'lng' => $lng,
        
        'email' => '', //$email,
        'reference' => $reference_field,
        'imageid' => $fid,
        'timestamp' => $timestamp,
      ];
    
      \Drupal::database()->insert('pin_drop_location')
        ->fields($fields)
        ->execute();
        
    $this->messenger()->addMessage($this->t('Location saved successfully.{$lat}, {$lng}'));
  }

//handled in the config form to get a base location
  /*function get_server_location() {
    $url = 'http://ip-api.com/json/';
    $response = file_get_contents($url);
    $location_data = json_decode($response, TRUE);
  
    if ($location_data && $location_data['status'] === 'success') {
      return [
        'latitude' => $location_data['lat'],
        'longitude' => $location_data['lon'],
      ];
    }
  
    // Fallback location if API fails.
    return [
      'latitude' => 0.0,
      'longitude' => 0.0,
    ];
  }*/ 
//TODO: work this out for easy navigation of maps
  function get_user_ip() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
      return $_SERVER['REMOTE_ADDR'];
    }
  }
  
  


}
