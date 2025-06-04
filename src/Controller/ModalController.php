<?php
/**
 * Create a modal page that presents a countrry dropdown or POSTAL CODE text fied 
 * See how to identify a close procimity to ones' location like city
 * Use Ajax to update form with guess as selected city or postal code suggestion 
 * if fails give location by form fields. 
 */
namespace Drupal\pin_drop_location\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller for displaying the modal example page.
 */
class ModalController extends ControllerBase {

  /**
   * Returns a render array for the modal example page.
   */
  public function modalPage() {
    return [
      '#theme' => 'modal_example_page',
      '#attached' => [
        'library' => [
          'pin_drop_location/modal_example', // Attach the Bootstrap and custom library.
        ],
      ],
      '#some_variable' => 'This is passed to Twig',
    ];

  }

}
