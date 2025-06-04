(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.googleMapBehavior = {
    attach: function (context, settings) {
      // Ensure the behavior runs only once per element
      const $mapElement = $('#map', context).once('googleMapBehavior');
      
      if ($mapElement.length) {
        const apiKey = drupalSettings.google_map.api_key; // API key from settings
        const mapId = drupalSettings.google_map.map_id || 'DEMO_MAP_ID'; // Optional map ID
        const defaultPosition = {
          lat: drupalSettings.google_map.default_lat || -25.344, // Default latitude
          lng: drupalSettings.google_map.default_lng || 131.031, // Default longitude
        };

        async function initMap() {
          try {
            // Request needed libraries from Google Maps
            //@ts-ignore
            const { Map } = await google.maps.importLibrary("maps");
            const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");

            // Initialize the map
            const map = new Map($mapElement[0], {
              zoom: 4,
              center: defaultPosition,
              mapId: mapId,
            });

            // Add a marker at the default position
            new AdvancedMarkerElement({
              map: map,
              position: defaultPosition,
              title: "Default Location",
            });
          } catch (error) {
            console.error("Google Maps failed to initialize:", error);
          }
        }

        // Load the Google Maps script dynamically if not already loaded
        if (!window.google || !window.google.maps) {
          const script = document.createElement('script');
          script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&callback=initMap`;
          script.async = true;
          script.defer = true;
          document.head.appendChild(script);
        } else {
          // Initialize the map directly if the script is already loaded
          initMap();
        }
      }
    },
  };
})(jQuery, Drupal, drupalSettings);
