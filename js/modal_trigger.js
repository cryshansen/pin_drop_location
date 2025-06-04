(function ($, Drupal) {
    Drupal.behaviors.modalTrigger = {
      attach: function (context, settings) {
        $(document).ready(function () {
          const modal = new bootstrap.Modal(document.getElementById('welcomeModal'));
          modal.show();
        });
      },
    };
  })(jQuery, Drupal);
  