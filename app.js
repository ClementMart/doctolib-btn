(function($) {
    $(document).ready(function() {

        if ($('.doctolib-color-picker').length) {
            $('.doctolib-color-picker').wpColorPicker();
        }

        var btn = $('.doctolib_btn');
        if (btn.length) {
            var delay = parseInt(btn.data('delay'), 10) || 0;
            setTimeout(function() {
                btn.addClass('doctolib_btn_visible');
            }, delay * 1000);
        }

    });
})(jQuery);
