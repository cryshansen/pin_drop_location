(function (Drupal, drupalSettings) {
  Drupal.behaviors.customLocationMap = {
    attach: function (context, settings) {
      const mapContainer = document.getElementById('google-map');
      if (mapContainer && drupalSettings.custom_location) {
        const { latitude, longitude } = drupalSettings.custom_location;

        const map = new google.maps.Map(mapContainer, {
          center: { lat: parseFloat(latitude), lng: parseFloat(longitude) },
          zoom: 14,
        });

        new google.maps.Marker({
          position: { lat: parseFloat(latitude), lng: parseFloat(longitude) },
          map: map,
        });
      }
    },
  };
})(Drupal, drupalSettings);
