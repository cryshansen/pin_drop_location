<?php
namespace Drupal\pin_drop_location\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Database\Database;
use Drupal\file\Entity\File;

/**
 * Provides a block to display recent pin drops.
 *
 * @Block(
 *   id = "recent_pin_drops_block",
 *   admin_label = @Translation("Recent Pin Drops"),
 * )
 */
class RecentPinDropsBlock extends BlockBase {

  public function build() {
    $build = [];
    $connection = Database::getConnection();
    
    $query = $connection->select('pin_drop_location', 'p')
      ->fields('p', ['lat', 'lng', 'reference', 'imageid', 'timestamp'])
      ->orderBy('timestamp', 'DESC')
      ->range(0, 5); // Show latest 5 entries

    $results = $query->execute()->fetchAll();

    $items = [];

    foreach ($results as $record) {
      $image_url = '';

      if ($record->image) {
        $file = File::load($record->image);
        if ($file) {
          $uri = $file->getFileUri();
          $image_url = file_create_url($uri);
        }
      }

      $items[] = [
        '#markup' => '<div class="pin-entry">
            <strong>Email:</strong> ' . htmlspecialchars($record->email) . '<br>
            <strong>Lat:</strong> ' . $record->lat . ', <strong>Lng:</strong> ' . $record->lng . '<br>' .
            ($image_url ? '<img src="' . $image_url . '" alt="Pin image" style="max-width: 150px;" />' : '') .
            '<hr></div>',
      ];
    }

    $build['recent_pins'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['recent-pins']],
      'items' => $items,
    ];

    return $build;
  }
}
