'use strict';

(function ($) {

    var debug = dbt_object;

    debug.init = function () {
        debug.bar = $('#dbt-bar');

        debug.bar.on('click', 'a.dbt-toggle', debug.toggle);

        debug.$refs_content = debug.bar.find('.refs-content');
        debug.bar.on('click', '.refs a, .settings', debug.get_ref);
        debug.bar.on('change', '#dbt-settings input', debug.save_settings);
        debug.$refs_content.on('click', '.bg', debug.hide_ref);
    };

    debug.toggle = function (e) {
        e.preventDefault();
        console.log('asdf');
        debug.bar.toggleClass('dbt-hidden');
    };

    debug.get_ref = function (e) {
        e.preventDefault();
        var  $this = $(this);

        debug.$refs_content.show();
        debug.$refs_content.find($this.attr('href')).show();
    };

    debug.hide_ref = function (e) {
        e.preventDefault(e);
        var  $this = $(this);
        debug.$refs_content.find('.ref-item:visible').hide();
        debug.$refs_content.hide();
    };

    debug.save_settings = function (e) {
        e.preventDefault();
        var $this = $(this), val = 0;

        if ($this.attr('type') === 'checkbox' ) {
            val = ($this.prop('checked')) ? 1 : 0;
        }

        $.post(debug.ajax_url, {
            action: 'dbt_save_setting',
            name: $this.attr('id'),
            value: val
        }, function (response) {
            console.log(val, response);
        });

    };

    $(document).ready(debug.init);

})(jQuery);