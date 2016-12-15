'use strict';

(function ($) {

    var debug = {};

    debug.init = function () {
        debug.bar = $('#wp-debug-bar');
        $(document).keydown(debug.show);
        debug.$refs_content = debug.bar.find('.refs-content');
        debug.bar.on('click', '.debug-refs a', debug.get_ref);
        debug.$refs_content.on('click', '.bg', debug.hide_ref);
    };

    debug.show = function (e) {
        if (e.keyCode == 68 && e.altKey && e.ctrlKey && e.shiftKey) debug.bar.toggle();
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