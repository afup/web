(function ($) {
    $(document).ready(function () {
        $('.js-checkall').click(function () {
            $(this).parents('table.afup_tab').find('input').each(function () {
                this.checked = true;
            });
        });
    });
})(jQuery);
