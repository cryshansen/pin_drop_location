(function ($, Drupal,drupalSettings) {
  Drupal.behaviors.googleMap = {
    attach: function (context, settings) {
      const apiKey = settings.pin_drop_location.api_key;
      const latitudeFieldId = settings.pin_drop_location.latitude;
      const longitudeFieldId = settings.pin_drop_location.longitude;

      if (!settings.pin_drop_location || !settings.pin_drop_location.api_key) {
        console.warn("Google Maps API key is missing.");
        return;
        
      }
      

      // Check if Google Maps API is already loaded
      if (typeof google === 'object' && typeof google.maps === 'object') {
          console.log("false --- initialize map");
        initializeMap();
      } else {
          console.log("true -- load map here.");
        // Load the Google Maps API script only if not already loaded
        const script = document.createElement('script');
        script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}`;
        script.async = true;
        script.defer = true;
        script.onload = initializeMap;
        document.head.appendChild(script);
      }


      function initializeMap() {
        const map = new google.maps.Map(document.getElementById('map'), {
          center: { lat: 37.7749, lng: -122.4194 },
          zoom: 10,
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



        /*
        const marker = new google.maps.Marker({
          position: { lat: 37.7749, lng: -122.4194 },
          map: map,
          draggable: true,
        });
        */

        google.maps.event.addListener(map, 'click', function(event) {
          const lat = event.latLng.lat();
          const lng = event.latLng.lng();
          document.getElementById('latitude').value = event.latLng.lat();
          document.getElementById('longitude').value = event.latLng.lng();
          
          // Place or move the pin to the clicked location
          new google.maps.Marker({
            position: event.latLng,
            map: map,
            title: 'Dropped Pin',
          });
          
          
         /* marker.setPosition(event.latLng);

          $(`#${latitudeFieldId}`).val(lat);
          $(`#${longitudeFieldId}`).val(lng);
          */
        });
        
        
        
      }//end initialize map
      
      
    }
  };
})(jQuery, Drupal);
