(function ($, Drupal, once) {
  Drupal.behaviors.sign_widget = {
    attach: function (context) {
      $(once('initiate-signature-pad', '.signature-pad', context)).each(function () {
        function resizeCanvas(canvas) {
          let ratio = Math.max(window.devicePixelRatio || 1, 1);
          canvas.width = canvas.offsetWidth * ratio;
          canvas.height = canvas.offsetHeight * ratio;
          canvas.getContext("2d").scale(ratio, ratio);
        }
        $(once('canvas-signature-pad', 'canvas.signature-pad', context)).each(function (index) {
          let setting_pad = $(this).data();
          let index_pad = $(this).attr('id');
          let canvas = document.getElementById(index_pad);
          let background = '';
          // Assign the hidden field (that saves the signature) to a variable.
          background = $(this).closest('.signature')
            .width($(this).width())
            .height($(this).height())
            .find(".background");
          // Instantiate the signaturepad itself.
          window[index_pad] = new SignaturePad(canvas, setting_pad);
          window[index_pad].addEventListener("afterUpdateStroke", () => {
            let data = window[index_pad].toDataURL('image/png');
            $('#' + index_pad + '-sign').val(data);
          });

          if (background && background.attr('src')) {
            window[index_pad].fromDataURL(background.attr('src'));
          }
          window.addEventListener("resize", function () {
            resizeCanvas(canvas);
          });
        });
        // Toolbox buttons clear.
        $(once('clear-signature-pad', '.signature_panel .clear', context)).on('click', function () {
          let index_pad = $(this).data('id');
          window[index_pad].clear();
          $(this).closest('.esign_container').find(".signature-storage").val('');
        });
        $(once('remove-signature-pad', '.signature_panel .clear', context)).on('dblclick', function () {
          let index_pad = $(this).data('id');
          window[index_pad].clear();
          $(this).closest('.esign_container').find("input").val('');
          $(this).closest('.signature_panel').find("img").remove();
        });
        // Fomatter buttons save.
        $(once('save-signature-pad', '.signature_panel .save', context)).on('click', function () {
          const index_pad = $(this).data('id');
          let data = $(this).closest('.esign_container').find(".signature-storage").data();
          if(data === undefined) {
            return;
          }
          data.sign = $(this).closest('.esign_container').find(".signature-storage").val();
          $(this).parent().remove();
          $.ajax({
            url: Drupal.url('ajax/sign_widget/sendSign/' + index_pad),
            type: 'POST',
            cache: false,
            contentType: "application/x-www-form-urlencoded;charset=UTF-8",
            data: data,
            success: function (data) {
              $('#' + data[0].selector + '-sign').closest('.esign_container').parent().html(
                $('<img>',{src:data[0].sign_src})
              );
            }
          });

        });
        //Show toolbox to change color.
        $(once('toolbox-signature-pad', '.signature_panel .toolbox input', context)).on('change', function () {
          let index_pad = $(this).data('id');
          let type = $(this).data('type');
          window[index_pad][type] = $(this).val();
          $('#' + index_pad).data(type, $(this).val());
          if (type == 'dotSize') {
            window[index_pad].maxWidth = $(this).val();
            $('#' + index_pad).data('maxWidth', $(this).val());
          }
        });
      });
    }
  };

  Drupal.AjaxCommands.prototype.sendSign = function (ajax, response, status) {
    $('#' + index_pad)
      .find('input[type="hidden"]')
      .trigger('sendSign');
  };
}(jQuery, Drupal, once));
