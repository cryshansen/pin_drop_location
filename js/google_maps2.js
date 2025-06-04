(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.googleMap = {
    attach: function (context, settings) {
      // Ensure this runs only once in the page lifecycle
      if (!settings.pin_drop_location || !settings.pin_drop_location.api_key) {
        console.warn("Google Maps API key is missing.");
        return;
      }

      const apiKey = settings.pin_drop_location.api_key;
      const latitudeFieldId = settings.pin_drop_location.latitude;
      const longitudeFieldId = settings.pin_drop_location.longitude;

      // Check if the Google Maps API script is already loaded
      if (!document.getElementById("google-maps-api")) {
        // Create and append the script tag with an ID
        const script = document.createElement("script");
        script.id = "google-maps-api";
        script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}`;
        script.async = true;
        script.defer = true;

        script.onload = function () {
          initializeMap();
        };

        document.head.appendChild(script);
      } else if (typeof google === "object" && typeof google.maps === "object") {
        // Initialize map if the script is already loaded
        initializeMap();
      }

      // Function to initialize the map
      function initializeMap() {
        const mapElement = document.getElementById("map");
        if (mapElement) {
          const map = new google.maps.Map(mapElement, {
            center: {
              lat: parseFloat(settings.pin_drop_location.server_latitude),
              lng: parseFloat(settings.pin_drop_location.server_longitude),
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

                map.setCenter(userLocation);
                map.setZoom(12);

                // Place a marker at the user's location
                new google.maps.Marker({
                  position: userLocation,
                  map: map,
                  title: "Your Location",
                });

                const latitudeInput = document.getElementById(latitudeFieldId);
                const longitudeInput = document.getElementById(longitudeFieldId);
                if (latitudeInput && longitudeInput) {
                  latitudeInput.value = userLocation.lat;
                  longitudeInput.value = userLocation.lng;
                }
              },
              () => {
                console.warn("Geolocation permission denied or unavailable.");
              }
            );
          } else {
            console.warn("Geolocation is not supported by this browser.");
          }

          // Add click listener to the map
          google.maps.event.addListener(map, "click", function (event) {
            const lat = event.latLng.lat();
            const lng = event.latLng.lng();
            document.getElementById(latitudeFieldId).value = lat;
            document.getElementById(longitudeFieldId).value = lng;

            // Place a pin at the clicked location
            new google.maps.Marker({
              position: event.latLng,
              map: map,
              title: "Dropped Pin",
            });
          });
        }
      }
    },
  };
})(jQuery, Drupal, drupalSettings);
