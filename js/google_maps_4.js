(function (Drupal, drupalSettings) {
  Drupal.behaviors.pinDropMapLoader = {
    attach: function (context, settings) {
      // Ensure this runs only once by checking a flag.
      if (document.body.dataset.mapLoaded) {
        return;
      }

      // Set a flag to prevent multiple executions.
      document.body.dataset.mapLoaded = true;

      // Wait for the DOM to fully load.
      document.addEventListener('DOMContentLoaded', function () {
        if (typeof google !== 'undefined' && google.maps) {
          // If the API is already loaded, initialize the map.
          initializeMap();
        } else {
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
        }
      });
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

        // Try to get user's current location
        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(
            (position) => {
              const userLocation = {
                lat: position.coords.latitude,
                lng: position.coords.longitude,
              };

              // Center map on user's location
              map.setCenter(userLocation);
              map.setZoom(12);

              // Place a marker at user's location
              new google.maps.Marker({
                position: userLocation,
                map: map,
                title: 'Your Location',
              });

              // Set form fields with user's location (if form exists)
              const latitudeInput = document.getElementById(latitudeFieldId);
              const longitudeInput = document.getElementById('longitude');
              if (latitudeInput && longitudeInput) {
                latitudeInput.value = userLocation.lat;
                longitudeInput.value = userLocation.lng;
              }
            },
            () => {
              console.warn('Geolocation permission denied or unavailable.');
            }
          );
        } else {
          console.warn('Geolocation is not supported by this browser.');
        }


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
