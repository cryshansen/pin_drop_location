(function (Drupal, drupalSettings) {
  Drupal.behaviors.pinDropMapLoader = {
    attach: function (context, settings) {
      if (typeof google !== 'undefined' && google.maps) {
        // Google Maps API is already loaded, initialize the map.
        initializeMap();
        return;
      }

      // Load Google Maps API dynamically.
      const script = document.createElement('script');
      script.src = `https://maps.googleapis.com/maps/api/js?key=${drupalSettings.pin_drop_location.api_key}`;
      script.async = true;
      script.defer = true;

      script.onload = function () {
        // Initialize the map after the API has loaded.
        initializeMap();
      };

      document.head.appendChild(script);
    },
  };

  // Function to initialize the map.
  function initializeMap() {
    const mapElement = document.getElementById('map');
    if (mapElement) {
      const map = new google.maps.Map(mapElement, {
        center: {
          lat: parseFloat(drupalSettings.pin_drop_location.server_latitude),
          lng: parseFloat(drupalSettings.pin_drop_location.server_longitude),
        },
        zoom: 8,
      });

      new google.maps.Marker({
        position: {
          lat: parseFloat(drupalSettings.pin_drop_location.server_latitude),
          lng: parseFloat(drupalSettings.pin_drop_location.server_longitude),
        },
        map: map,
        title: 'Your Location',
      });
    }
  }
})(Drupal, drupalSettings);
