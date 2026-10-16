(function ($) {
    "use strict";

    /**
     * Init the parallaxie plugin ( assets/js/parallaxie.js ) on every
     * .parallaxie element. The plugin reads data-image, falling back to the
     * element's own inline background-image, so no options are needed here.
     */
    function initovinxParallaxie($scope) {
        var $items;

        if ($scope && $scope.length) {
            $items = $scope.find(".parallaxie");

            if ($scope.hasClass("parallaxie")) {
                $items = $items.add($scope);
            }
        } else {
            $items = $(".parallaxie");
        }

        if (!$items.length || typeof $.fn.parallaxie !== "function") {
            return;
        }

        $items.parallaxie();
    }

    $(window).on("elementor/frontend/init", function () {
        if (typeof elementorFrontend !== "undefined" && elementorFrontend.hooks) {
            elementorFrontend.hooks.addAction(
                "frontend/element_ready/ovinx_banner_parallax.default",
                function ($scope) {
                    initovinxParallaxie($scope);
                }
            );
        }
    });

    $(function () {
        initovinxParallaxie();
    });

})(jQuery);
