'use strict';

(function ($) {

    var debug = {};

    debug.init = function () {
        debug.bar = $('#wp-debug-bar');
        $(document).keydown(debug.toggle);
        debug.$refs_content = debug.bar.find('.refs-content');
        debug.bar.on('click', '.debug-refs a', debug.get_ref);
        debug.bar.on('click', 'a.hide-bar', debug.hide);
        debug.$refs_content.on('click', '.bg', debug.hide_ref);
    };

    debug.toggle = function (e) {
        if (e.keyCode == 68 && e.altKey && e.ctrlKey && e.shiftKey) debug.bar.toggle();
    };
    debug.hide = function (e) {
        e.preventDefault();
        debug.bar.hide();
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
        debug.$refs_content.hide();
    };

    $(document).ready(debug.init);

})(jQuery);