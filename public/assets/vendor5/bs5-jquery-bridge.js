/*!
 * BS3 -> BS5 jQuery plugin bridge (GestionVilles)
 * Reexpose $.fn.modal/tooltip/popover/dropdown/tab/collapse/carousel/
 * alert/button/offcanvas/scrollspy au-dessus des classes Bootstrap 5,
 * pour que les scripts legacy (AdminLTE 2, inits inline) fonctionnent
 * sans modification. Doit etre charge APRES jQuery + bootstrap.bundle.
 */
(function () {
    if (typeof bootstrap === 'undefined' || typeof jQuery === 'undefined') {
        return;
    }
    var $ = jQuery;

    function inst(name, el, opts) {
        var C = bootstrap[name];
        if (!C) { return null; }
        return C.getOrCreateInstance ? C.getOrCreateInstance(el, opts || {})
                                     : new C(el, opts || {});
    }

    function makePlugin(name) {
        var lname = name.toLowerCase();
        $.fn[lname] = function (action, opts) {
            var ret;
            this.each(function () {
                var cfg = (typeof action === 'object' && action) || opts || {};
                var i = inst(name, this, cfg);
                if (!i) { return; }
                if (typeof action !== 'string' || action === '') { return; }
                var m = action === 'destroy' ? 'dispose' : action;
                if (lname === 'dropdown' && action === 'toggle') { m = 'toggle'; }
                if (typeof i[m] === 'function') {
                    var r = i[m](opts);
                    if (r !== undefined) { ret = r; }
                }
            });
            return ret !== undefined ? ret : this;
        };
    }

    ['Modal', 'Tooltip', 'Popover', 'Collapse', 'Dropdown', 'Tab',
     'Carousel', 'Offcanvas', 'Alert', 'Button', 'ScrollSpy'].forEach(makePlugin);

    // jquery-ui surchargeait tooltip/popover en BS3 aussi : garder le
    // plugin jquery-ui accessible sous un alias au cas ou.
    if ($.fn.tooltip && $.ui && $.ui.tooltip) {
        $.fn.uiTooltip = $.ui.tooltip;
    }
})();
