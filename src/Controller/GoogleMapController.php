<?php

namespace Drupal\pin_drop_location\Controller;

use Drupal\Core\Controller\ControllerBase;

class GoogleMapController extends ControllerBase {
  /**
   * Display the Google Map.
   *
   * @return array
   *   Render array for the Google Map page.
   */
  public function displayMap() {
    // Load configuration settings.
    $config = \Drupal::config('pin_drop_location.settings');
    $api_key = $config->get('api_key');
    $default_lat = (float) $config->get('default_lat');
    $default_lng = (float) $config->get('default_long');
    \Drupal::logger('pin_drop_location')->info('config default api key  @message @lng, @lat', [
      '@message' => $api_key,
      '@lng' => $default_lng,
      '@lat' => $default_lat,
    ]);
    // Pass values to the Twig template.
    return [
      '#theme' => 'google_map',
      '#api_key' => $api_key,
      '#lat' => $default_lat,
      '#lng' => $default_lng,
      '#attached' => [
        'library' => ['pin_drop_location/google_map'], // Optional for additional JS/CSS libraries.
        'drupalSettings' => [
            'pin_drop_location' => [
              'api_key' => $api_key,
              'latitude' => $default_lat,
              'longitude' => $default_lng,
            ],
          ],
      ],
    ];

  }
}
//42.893 long -78.8753  new york  40.7128° N, 74.0060° W