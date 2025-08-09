jQuery(document).ready(function($) {
    function trigger_variation_change() {
        $('form.variations_form').trigger('woocommerce_variation_select_change');
        $('form.variations_form').trigger('check_variations');
    }

    $('.ns-vr-swatches-container').on('click', '.ns-vr-swatch', function(e) {
        e.preventDefault();

        var $swatch = $(this);
        var $container = $swatch.closest('.ns-vr-swatches-container');
        var $select = $container.next('.original-variation-select').find('select');
        var value = $swatch.data('value');

        if ($swatch.hasClass('selected')) {
            // If the swatch is already selected, deselect it.
            $swatch.removeClass('selected');
            $select.val('').trigger('change');
        } else {
            // Deselect other swatches in the same container.
            $container.find('.ns-vr-swatch').removeClass('selected');
            // Select the clicked swatch.
            $swatch.addClass('selected');
            // Update the hidden select dropdown.
            $select.val(value).trigger('change');
        }

        trigger_variation_change();
    });

    // When the "Clear" button is clicked
    $('.variations_form').on('click', '.reset_variations', function() {
        $('.ns-vr-swatch.selected').removeClass('selected');
    });

    // When a variation is found, make sure the right swatches are selected
    $(document).on('found_variation', 'form.variations_form', function( event, variation ) {
        $.each(variation.attributes, function(attribute, value){
            var $swatch = $('.ns-vr-swatch[data-value="' + value + '"]');
            if ($swatch.length) {
                var $container = $swatch.closest('.ns-vr-swatches-container');
                $container.find('.ns-vr-swatch').removeClass('selected');
                $swatch.addClass('selected');
            }
        });
    });
});
