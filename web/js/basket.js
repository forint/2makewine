var request = false;

jQuery(document).ready(function() {

    jQuery('form[id^="form_add_basket"]').on("submit", function (e) {

        $('.ui-spinner-input').removeAttr('disabled');

        if (typeof e.originalEvent !== 'undefined'){

            e.preventDefault();

            var clickedElement = e.originalEvent.explicitOriginalTarget;

            if (!$(clickedElement).hasClass('add-product')){
                return false;
            }

            if (false === request) {
                request = true;
                var self = $(this);

                jQuery.ajax({
                    type: self.attr('method'),
                    url: self.attr('action'),
                    data: self.serialize(),
                    beforeSend: function () {
                        $('#preload-background').show();
                        $('.preload__wrap').show();
                    },
                    success: function (data) {
                        if (data) {
                            $('.preload__wrap').hide();
                            $('#preload-background').hide();
                            jQuery(".cart-drop").replaceWith(data);
                            $('html, body').animate({scrollTop: '0px'}, 400, function () {
                                request = false;
                                jQuery(".cart-drop").show();
                            });
                        }
                    }/*
                    success: function (data) {
                        if (data) {
                            request = false;
                            console.log(data);
                            console.log(self.attr('data-target');
                            jQuery(self.attr('data-target')).html(data).modal('show');
                        }
                    }*/
                });
            } else {
                return false;
            }
        }
    });

});