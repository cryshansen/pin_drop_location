<?php
/**
 * 
 * this page is attempting to display the map within a page controller bu uses the form twig which is hard coded.
 */
namespace Drupal\pin_drop_location\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\pin_drop_location\Form\PinDropLocationForm;
use Drupal\Core\Render\RendererInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Database\Database;
use Drupal\file\Entity\File;

class PinDropLocationController extends ControllerBase {

  /**
   * Renders the pin location form with a map.
   *
   * @return array
   *   A render array containing the map form.
   */
  public function mapPage() {
     // Retrieve the API key from the configuration.
     $config = $this->config('pin_drop_location.settings');
     $api_key = $config->get('api_key');
     $default_lat = (float) $config->get('default_lat');
     $default_lng = (float) $config->get('default_lng');
     //$api_key = 'AIzaSyBSXa3AhfDfHewHqNU73yBuhjW-80SBfNk';
     $form = $this->formBuilder()->getForm(PinDropLocationForm::class);
     //$form['#attached']['library'][] = 'pin_drop_location/google_map';


    // Attach the JavaScript library and pass the API key to drupalSettings.
    $build = [
      '#theme' => 'google_map',
      '#attached' => [
        'library' => ['pin_drop_location/google_map'],
        'drupalSettings' => [
            'pin_drop_location' => [
              'api_key' => $api_key,
              'latitude' => $default_lat,
              'longitude' => $default_lng,
            ],
        ],
      ],
      '#markup' => t('<div id="map" style="height: 400px;"></div>'),//Markup::create('<div id="map" style="height: 400px;"></div>'),
      'form' => $form,
    ];

    return $build;
  }
  
  
  public function recentPinsPage() {
      $connection = Database::getConnection();
    /*\Drupal::database()->select('pin_drop_location')
        ->fields($fields)
        ->execute();*/
      $query = $connection->select('pin_drop_location', 'p')
        ->fields('p', ['id', 'lat', 'lng','reference', 'imageid', 'timestamp'])
        ->orderBy('timestamp', 'DESC')
        ->range(0, 10); // Show latest 10
    
      $results = $query->execute()->fetchAll();
      $items = [];
    
      foreach ($results as $record) {
        $image_url = '';
        if ($record->imageid) {
          $file = File::load($record->imageid);
          if ($file) {
            $uri = $file->getFileUri();
            $image_url = \Drupal::service('file_url_generator')->generateAbsoluteString($uri);
            // deprecated d10^ $image_url = file_create_url($uri);
          }
        }
    
        $items[] = [
          '#markup' => '<div class="pin-entry">
            <strong>Reference:</strong> ' . htmlspecialchars($record->reference) . '<br>
            <strong>Lat:</strong> ' . $record->lat . ', <strong>Lng:</strong> ' . $record->lng . '<br>' .
            ($image_url ? '<img src="' . $image_url . '" alt="Pin image" style="max-width: 150px;" />' : '') .
            '<hr></div>',
        ];
      }
    
      return [
        '#type' => 'container',
        '#attributes' => ['class' => ['recent-pins']],
        'items' => $items,
        '#title' => $this->t('Recent Pin Drops'),
      ];
    }
  
}