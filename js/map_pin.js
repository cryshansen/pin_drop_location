(function (Drupal, drupalSettings) {
    Drupal.behaviors.pinLocationMap = {
      attach: function (context, settings) {
       console.log("pinLocationMap attached");
       
       async function initMap() {
        // Request needed libraries.
        const { Map } = await google.maps.importLibrary("maps");
        const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");
        const map = new google.maps.Map(document.getElementById("map"), {
          zoom: 4,
          center: { lat: -25.363882, lng: 131.044922 },
          mapId: "DEMO_MAP_ID",
        });
        const bounds = {
          north: -25.363882,
          south: -31.203405,
          east: 131.044922,
          west: 125.244141,
        };
      
        // Display the area between the location southWest and northEast.
        map.fitBounds(bounds);
      
        // Add 5 markers to map at random locations.
        // For each of these markers, give them a title with their index, and when
        // they are clicked they should open an infowindow with text from a secret
        // message.
        const secretMessages = ["This", "is", "the", "secret", "message"];
        const lngSpan = bounds.east - bounds.west;
        const latSpan = bounds.north - bounds.south;
      
        for (let i = 0; i < secretMessages.length; ++i) {
          const marker = new google.maps.marker.AdvancedMarkerElement({
            position: {
              lat: bounds.south + latSpan * Math.random(),
              lng: bounds.west + lngSpan * Math.random(),
            },
            map: map,
          });
      
          attachSecretMessage(marker, secretMessages[i]);
        }
      }
      
      // Attaches an info window to a marker with the provided message. When the
      // marker is clicked, the info window will open with the secret message.
      function attachSecretMessage(marker, secretMessage) {
        const infowindow = new google.maps.InfoWindow({
          content: secretMessage,
        });
      
        marker.addListener("click", () => {
          infowindow.open(marker.map, marker);
        });
      }
      
      initMap();
      }
    };
  })(Drupal, drupalSettings);
  