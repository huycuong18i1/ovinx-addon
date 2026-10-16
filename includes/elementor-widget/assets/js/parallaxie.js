(function($){

    $.fn.parallaxie = function(options){

        options = $.extend({
            speed: 0.2,
            repeat: 'no-repeat',
            size: 'cover',
            pos_x: 'center',
            offset: 0,
        }, options );

        this.each(function(){
            var $el = $(this);
            var local_options = $el.data('parallaxie');
            if (typeof local_options !== 'object') local_options = {};
            local_options = $.extend({}, options, local_options);

            var image_url = $el.data('image');

            if (typeof image_url === 'undefined' || !image_url) {
                image_url = $el.css('background-image');

                if (!image_url || image_url === 'none') return;

                image_url = image_url.replace(/^url\(["']?/, '').replace(/["']?\)$/, '');
            }

            var img = new Image();
            img.onload = function() {
                initParallax($el, image_url, local_options);
            };
            img.onerror = function() {
                console.warn('Parallaxie: Không load được ảnh ->', image_url);
            };
            img.src = image_url;
        });

        return this;
    };

    function initParallax($el, image_url, local_options) {
        // APPLY DEFAULT CSS
        var pos_y = local_options.offset + ($el.offset().top - $(window).scrollTop()) * (1 - local_options.speed);
        $el.css({
            'background-image': 'url("' + image_url + '")',
            'background-size': local_options.size,
            'background-repeat': local_options.repeat,
            'background-attachment': 'fixed',
            'background-position': local_options.pos_x + ' ' + pos_y + 'px',
        });

        parallax_scroll($el, local_options);

        $(window).on('scroll', function(){
            parallax_scroll($el, local_options);
        });
    }

    function parallax_scroll($el, local_options){
        var pos_y = local_options.offset + ($el.offset().top - $(window).scrollTop()) * (1 - local_options.speed);
        $el.css('background-position', local_options.pos_x + ' ' + pos_y + 'px');
    }

}(jQuery));
