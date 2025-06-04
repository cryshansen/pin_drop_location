(function ($, Drupal) {
  Drupal.behaviors.googleMapBehavior = {
    attach: function (context) {
      const $mapElement = $('#map', context);
      if ($mapElement.length) {
        initMap($mapElement[0]);
      }
      
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
      
      
      
      
      
    },
  };
})(jQuery, Drupal);
