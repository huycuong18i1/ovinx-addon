;(function ($) {

    "use strict";

    // "Ovinx Privacy Policy" widget: sticky section nav (`.list-name-privacy`)
    // with click-to-scroll and scroll-spy "active" state.
    //
    // Rebuilt from ground truth's `main.js` `clickScroll()` + `privacy()`, which
    // are document-wide and have a few problems for a WordPress/Elementor page:
    //  - the scroll handler re-queries the DOM + `querySelector(id)` on EVERY
    //    scroll event (and throws on an id that isn't a valid selector);
    //  - nothing is bound per widget instance, so Elementor's editor re-render
    //    stacks duplicate listeners;
    //  - it always subtracts `header` height from the scroll target, even when
    //    the header isn't sticky, and ignores the fixed WP admin bar;
    //  - the last item never activates on a short page (its section can't reach
    //    the trigger line), and clicks flicker through every section in between.
    // This version is scoped to one widget instance, rAF-throttled, cleans up
    // after itself, and offsets the scroll by the real fixed bars.

    var ACTIVE_CLASS = "active";
    var TRIGGER_RATIO = 0.3;

    function getTarget(link) {
        var id = (link.getAttribute("href") || "").replace(/^#/, "");

        if (!id) {
            return null;
        }

        try {
            id = decodeURIComponent(id);
        } catch (e) {
            // keep raw id
        }

        return document.getElementById(id);
    }

    // Height of whatever is pinned to the top of the viewport while scrolling:
    // the fixed WP admin bar (>= 600px wide) and the theme's sticky header
    // (`body.header_sticky`, see themes/ovinx/js/main.js `headerFix()`).
    function getFixedOffset() {
        var offset = 0;
        var adminBar = document.getElementById("wpadminbar");

        if (adminBar && window.getComputedStyle(adminBar).position === "fixed") {
            offset += adminBar.offsetHeight;
        }

        if (document.body.classList.contains("header_sticky")) {
            var header = document.querySelector(".tf-header") || document.querySelector("header");

            if (header) {
                offset += header.offsetHeight;
            }
        }

        return offset;
    }

    function initPrivacyPolicy($scope) {
        var $root = $scope.find(".tf-privacy").first();

        if (!$root.length) {
            return;
        }

        var teardown = $root.data("tfPrivacyDestroy");
        if (typeof teardown === "function") {
            teardown();
        }

        var links = $root.find('.list-name-privacy a[href^="#"]').get();

        if (!links.length) {
            return;
        }

        var ticking = false;
        var locked = false;
        var lockTimer = null;

        function setActive(activeLink) {
            links.forEach(function (link) {
                link.classList.toggle(ACTIVE_CLASS, link === activeLink);
            });
        }

        function update() {
            ticking = false;

            if (locked) {
                return;
            }

            var triggerPoint = Math.max(window.innerHeight * TRIGGER_RATIO, getFixedOffset() + 30);
            var scrollBottom = window.pageYOffset + window.innerHeight;
            var atPageBottom = scrollBottom >= document.documentElement.scrollHeight - 2;
            var current = null;

            // Last section whose top has passed the trigger line.
            links.forEach(function (link) {
                var target = getTarget(link);

                if (target && target.getBoundingClientRect().top <= triggerPoint) {
                    current = link;
                }
            });

            // A short last section can never reach the trigger line.
            if (atPageBottom && window.pageYOffset > 0) {
                current = links[links.length - 1];
            }

            if (current) {
                setActive(current);
            }
        }

        function requestUpdate() {
            if (!ticking) {
                ticking = true;
                window.requestAnimationFrame(update);
            }
        }

        function unlock() {
            locked = false;
            window.clearTimeout(lockTimer);
            requestUpdate();
        }

        function onClick(e) {
            var target = getTarget(this);

            if (!target) {
                return;
            }

            e.preventDefault();

            var top = target.getBoundingClientRect().top + window.pageYOffset - getFixedOffset() - 15;

            // Keep the clicked item highlighted while the smooth scroll passes
            // over the sections in between.
            setActive(this);
            locked = true;
            window.clearTimeout(lockTimer);
            lockTimer = window.setTimeout(unlock, 1200);

            window.scrollTo({ top: Math.max(top, 0), behavior: "smooth" });
        }

        $root.on("click.tfPrivacy", ".list-name-privacy a[href^='#']", onClick);
        window.addEventListener("scroll", requestUpdate, { passive: true });
        window.addEventListener("resize", requestUpdate);
        window.addEventListener("scrollend", unlock);

        $root.data("tfPrivacyDestroy", function () {
            $root.off("click.tfPrivacy");
            window.removeEventListener("scroll", requestUpdate);
            window.removeEventListener("resize", requestUpdate);
            window.removeEventListener("scrollend", unlock);
            window.clearTimeout(lockTimer);
            $root.removeData("tfPrivacyDestroy");
        });

        update();
    }

    $(window).on("elementor/frontend/init", function () {
        elementorFrontend.hooks.addAction("frontend/element_ready/ovinx_privacy_policy.default", initPrivacyPolicy);
    });

})(jQuery);
