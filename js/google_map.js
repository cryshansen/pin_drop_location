(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.googleMap = {
    attach: function (context, settings) {
      // Prevent multiple initializations
      if (window.mapInitialized) {
        console.log("Map already initialized. Skipping...");
        return;
      }
      window.mapInitialized = true; // Set the flag

      console.log("Initializing Google Map...");

      const apiKey = drupalSettings.pin_drop_location.api_key || 'AIzaSyBSXa3AhfDfHewHqNU73yBuhjW-80SBfNk';
      const latitudeFieldId = drupalSettings.pin_drop_location.latitude;
      const longitudeFieldId = drupalSettings.pin_drop_location.longitude;
      const latitudeFieldValue = parseFloat(drupalSettings.pin_drop_location.server_latitude) || 45.534053;
      const longitudeFieldValue = parseFloat(drupalSettings.pin_drop_location.server_longitude) || -73.6262239;

      // Ensure lat/lng are numbers
      if (isNaN(latitudeFieldValue) || isNaN(longitudeFieldValue)) {
        console.error("Invalid latitude/longitude values.");
        return;
      }

      // Check if Google Maps API is already loaded
      if (typeof google === 'object' && typeof google.maps === 'object') {
        console.log("Google Maps API already loaded. Initializing map...");
        initializeMap();
      } else {
        console.log("Loading Google Maps API...");
        const script = document.createElement('script');
        script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}`;
        script.async = true;
        script.defer = true;
        script.onload = initializeMap;
        document.head.appendChild(script);
      }

      function initializeMap() {
        console.log("Map initialization started...");
        const map = new google.maps.Map(document.getElementById('map'), {
          center: { lat: latitudeFieldValue, lng: longitudeFieldValue },
          zoom: 17,
          mapId: 'map',
        });

        let marker = new google.maps.Marker({
          position: { lat: latitudeFieldValue, lng: longitudeFieldValue },
          map: map,
          draggable: true,
          title: "Drag me or click me!"
        });

        marker.addListener("click", function () {
          let position = marker.getPosition();
          alert(`Marker Position:\nLatitude: ${position.lat()}\nLongitude: ${position.lng()}`);
          document.getElementById('latitude').value = position.lat();
          document.getElementById('longitude').value = position.lng();
        });

        marker.addListener("dragend", function () {
          let position = marker.getPosition();
          console.log(`New Position - Lat: ${position.lat()}, Lng: ${position.lng()}`);
          document.getElementById('latitude').value = position.lat();
          document.getElementById('longitude').value = position.lng();
        });

        google.maps.event.addListener(map, 'click', function (event) {
          const lat = event.latLng.lat();
          const lng = event.latLng.lng();
          document.getElementById('latitude').value = lat;
          document.getElementById('longitude').value = lng;

          marker.setPosition(event.latLng); // Move marker to clicked position
        });
      } // End initializeMap
    } // End attach function
  };
})(jQuery, Drupal, drupalSettings);
