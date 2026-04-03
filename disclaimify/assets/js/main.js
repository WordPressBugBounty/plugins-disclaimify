(function ($) {
"use strict";
    $('input[name="include_condition"]').on('change', function() {
        const $this = $(this),
            $value = $this.val();
        if($value === 'show_on_all') {
            $('[data-dep-id="show_on_taxonomies"], [data-dep-id="show_on_posts"]').slideUp();
        } else {
            $(`[data-dep-id="${$value}"]`).slideDown().siblings().slideUp();
        }
    })
    $('input[name="exclude_condition"]').on('change', function() {
        const $this = $(this),
            $value = $this.val();
        if($value === 'disable') {
            $('[data-dep-id="hide_on_taxonomies"], [data-dep-id="hide_on_posts"]').slideUp();
        } else {
            $(`[data-dep-id="${$value}"]`).slideDown().siblings().slideUp();
        }
    })
})(jQuery);