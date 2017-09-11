/**
 * @link      http://ktreeportal.com/
 * @copyright Copyright (c) 2016 KTree.com.
 * @license   http://ktreeportal.com/license
 */
(function($) {

    if (typeof window.KTree == 'undefined') {
        window.KTree = {};
    }
    /**
     *
     */
    $.extend(KTree, {
        /**
         * Adds loader globally before ajax or some other actions.
         *
         * @param msg
         * @param container
         */
        addLoader: function(msg, container) {
            var text = (typeof msg == 'undefined') ? 'Please wait...' : msg;
            container = (typeof container == 'undefined') ? 'body' : container;
            var bodyHtml =
                '<div class="global-loader"><div class="ui-widget-overlay1"></div><div class="content-loader"><div class="loader"></div> ' +
                    text + '<div class="clear"></div></div></div>';
            $(container).append(bodyHtml);
        },
        removeLoader: function() {
            $('.global-loader').remove();
        }
    });
})(jQuery);
