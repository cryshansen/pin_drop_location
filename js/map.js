(function (Drupal, drupalSettings) {
    Drupal.behaviors.pinLocationMap = {
      attach: function (context, settings) {
        // Initialize the map (using Leaflet, for example).
        var map = L.map('map').setView([51.505, -0.09], 13);
  
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: 'Map data © OpenStreetMap contributors'
        }).addTo(map);
  
        var marker;
  
        // Listen for map clicks to set the pin location.
        map.on('click', function(e) {
          if (marker) {
            map.removeLayer(marker);
          }
          marker = L.marker(e.latlng).addTo(map);
          
          // Set lat/lng hidden form fields.
          document.getElementById('lat').value = e.latlng.lat;
          document.getElementById('lng').value = e.latlng.lng;
        });
      }
    };
  })(Drupal, drupalSettings);
  