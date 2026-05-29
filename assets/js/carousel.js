(function($) {
    'use strict';

    function scrollByStep($container, dir) {
        var node = $container.get(0);
        if (!node) {
            return;
        }

        var step = Math.max(200, Math.round(node.clientWidth * 0.8));
        node.scrollBy({ left: dir * step, behavior: 'smooth' });
    }

    $(document).on('click', '.ewgcs-carousel-prev', function() {
        var $wrap = $(this).closest('.ewgcs-carousel-widget');
        scrollByStep($wrap.find('.ewgcs-layout-carousel').first(), -1);
    });

    $(document).on('click', '.ewgcs-carousel-next', function() {
        var $wrap = $(this).closest('.ewgcs-carousel-widget');
        scrollByStep($wrap.find('.ewgcs-layout-carousel').first(), 1);
    });
})(jQuery);
