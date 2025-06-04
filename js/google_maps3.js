(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.googleMap = {
    attach: function (context, settings) {
      
      // Google API key
      const apiKey = settings.pin_drop_location.api_key;
      const latitudeFieldId = settings.pin_drop_location.latitude;
      const longitudeFieldId = settings.pin_drop_location.longitude;

      if (!settings.pin_drop_location || !apiKey) {
        console.warn("Google Maps API key is missing.");
        return;
      }

      // Function to initialize map
      function initializeMap(lat, lng) {
        const mapElement = document.getElementById('map');
        if (mapElement) {
          const map = new google.maps.Map(mapElement, {
            center: { lat: lat, lng: lng },
            zoom: 8,
          });
          new google.maps.Marker({
            position: { lat: lat, lng: lng },
            map: map,
            title: 'Selected Location',
          });
        }
      }

      // Function to show the popup
      function showCountryPopup() {
        const modalHtml = `
          <div id="countryModal" class="modal" style="display: block; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border: 1px solid #ccc; z-index: 1000;">
            <h3>Select Your Country</h3>
            <select id="countrySelect">
              <option value="">-- Select a Country --</option>
              <option value="US" data-lat="37.0902" data-lng="-95.7129">United States</option>
              <option value="CA" data-lat="56.1304" data-lng="-106.3468">Canada</option>
              <option value="GB" data-lat="55.3781" data-lng="-3.4360">United Kingdom</option>
              <option value="IN" data-lat="20.5937" data-lng="78.9629">India</option>
            </select>
            <button id="countryConfirm">Confirm</button>
          </div>
          <div id="modalBackdrop" style="display: block; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 999;"></div>
        `;

        $('body').append(modalHtml);

        // Handle confirmation
        $('#countryConfirm').on('click', function () {
          const selectedOption = $('#countrySelect option:selected');
          const lat = parseFloat(selectedOption.data('lat'));
          const lng = parseFloat(selectedOption.data('lng'));

          if (!isNaN(lat) && !isNaN(lng)) {
            // Set latitude and longitude fields
            $(`#${latitudeFieldId}`).val(lat);
            $(`#${longitudeFieldId}`).val(lng);

            // Remove the modal
            $('#countryModal').remove();
            $('#modalBackdrop').remove();

            // Initialize map with selected coordinates
            initializeMap(lat, lng);
          } else {
            alert('Please select a valid country.');
          }
        });
      }

      // Wait for DOM to load
      $(document).ready(function () {
        const latitudeInput = $(`#${latitudeFieldId}`);
        const longitudeInput = $(`#${longitudeFieldId}`);

        // Check if lat/lng fields are empty
        if (!latitudeInput.val() || !longitudeInput.val()) {
          showCountryPopup();
        } else {
          // Initialize map with existing coordinates
          const lat = parseFloat(latitudeInput.val());
          const lng = parseFloat(longitudeInput.val());
          initializeMap(lat, lng);
        }
      });
    },
  };
})(jQuery, Drupal, drupalSettings);
