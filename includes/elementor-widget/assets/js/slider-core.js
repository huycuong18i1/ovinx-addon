;(function($) {

    "use strict";

    // QA UX-011: Swiper's slideToLoop() schedules slideTo() in a requestAnimationFrame; when a widget re-initialises (we destroy the
    // old instance first) that frame can run on a destroyed instance whose `params` is gone -> console TypeError
    // "reading 'slidesPerGroupSkip'". Make slideTo a no-op on a destroyed instance. No visible behaviour change.
    if (window.Swiper && window.Swiper.prototype && !window.Swiper.prototype.__tfSafeSlideTo) {
        var tfOriginalSlideTo = window.Swiper.prototype.slideTo;
        window.Swiper.prototype.slideTo = function () {
            if (this.destroyed || !this.params) {
                return this;
            }
            return tfOriginalSlideTo.apply(this, arguments);
        };
        window.Swiper.prototype.__tfSafeSlideTo = true;
    }

function initSwiperInScope($scope) {
    $scope.find(".tfc-swiper").each(function (index, element) {
        var $container = $(element);

        if (element.swiper && typeof element.swiper.destroy === "function") {
            element.swiper.destroy(true, true);
        }

        var preview   = $container.data("preview") || 1;
        var tablet    = $container.data("tablet") || 1;
        var mobile    = $container.data("mobile") || 1;
        var mobileSm  = $container.data("mobile-sm") !== undefined ? $container.data("mobile-sm") : mobile;
        var laptop    = $container.data("laptop");
        var centerMode = $container.data("center") === true || $container.data("center") === "true";

        var spacing   = $container.data("space");
        var spacingMd = $container.data("space-md");
        var spacingLg = $container.data("space-lg");

        if (spacing !== undefined && spacingMd === undefined && spacingLg === undefined) {
            spacingMd = spacing;
            spacingLg = spacing;
        } else if (spacing === undefined && spacingMd !== undefined && spacingLg === undefined) {
            spacing = 0;
            spacingLg = spacingMd;
        }

        spacing   = spacing   || 0;
        spacingMd = spacingMd || 0;
        spacingLg = spacingLg || 0;

        // 575px and >=1440px tiers: widgets that only know the 3-value form fall back to the nearest value.
        var spacingSm = $container.data("space-sm");
        var spacingXl = $container.data("space-xl");
        spacingSm = (spacingSm === undefined) ? spacing   : (spacingSm || 0);
        spacingXl = (spacingXl === undefined) ? spacingLg : (spacingXl || 0);

        var perGroup   = $container.data("pagination")    || 1;
        var perGroupSm = $container.data("pagination-sm") || 1;
        var perGroupMd = $container.data("pagination-md") || 1;
        var perGroupLg = $container.data("pagination-lg") || 1;
        // >=1440px tier; widgets that only know the 1200px value fall back to it.
        var perGroupXl = $container.data("pagination-xl") || perGroupLg;

        var gridRows   = $container.data("grid") || 1;
        // Opt-in override for the two phone-width tiers (root/<575 and the
        // 575 breakpoint) — lets a widget ship a grid layout on
        // tablet/desktop but fall back to a plain single-row carousel on
        // phones, without touching every other widget already relying on
        // `data-grid` applying uniformly at every breakpoint (e.g.
        // `widget-blog.php` Style 3). Defaults to `gridRows` (today's exact
        // behavior) whenever `data-grid-mobile` isn't set.
        var gridRowsMobile = $container.data("grid-mobile");
        gridRowsMobile = (gridRowsMobile === undefined) ? gridRows : gridRowsMobile;
        var cursorType = $container.data("cursor") ?? false;
        var loop       = $container.data("loop") ?? false;
        var effect     = $container.data("effect") || "slide";
        var atPlay     = $container.data("auto");
        var pauseHover = $container.data("pause-hover");
        if (pauseHover === undefined) {
            pauseHover = true;
        }
        var speed      = $container.data("speed") || 800;
        var delay      = $container.data("delay") || 1000;
        var direction  = $container.data("direction") || "horizontal";
        // `data-init` = 0-based index of the slide to start on (Swiper's
        // `initialSlide`), e.g. "Ovinx Slideshow" Style 3 (carousel peek)
        // renders `data-init="1"` so it opens on slide 2, not slide 1.
        var initSlide  = parseInt($container.data("init"), 10) || 0;
        var slideCount = $container.find(".swiper-wrapper > .swiper-slide").length;
        if (initSlide >= slideCount) {
            initSlide = 0;
        }

        var $pagination = $container.find(".tf-sw-pagination").first();
        if (!$pagination.length) {
            $pagination = $container.closest(".tf-btn-swiper-main").find(".tf-sw-pagination").first();
        }
        if (!$pagination.length) {
            $pagination = $container.closest(".tf-btn-swiper-item").find(".tf-sw-pagination").first();
        }
        var $fraction   = $container.find(".pagination-fraction").first();

        var nextEl =
            $container.find(".nav-next-swiper").get(0) ||
            $container.closest(".tf-btn-swiper-main").find(".nav-next-swiper").get(0) ||
            $container.closest(".tf-btn-swiper-item").find(".nav-next-swiper").get(0) ||
            $container.closest(".box-swiper").find(".group-btn-slider .nav-next-swiper").get(0);

        var prevEl =
            $container.find(".nav-prev-swiper").get(0) ||
            $container.closest(".tf-btn-swiper-main").find(".nav-prev-swiper").get(0) ||
            $container.closest(".tf-btn-swiper-item").find(".nav-prev-swiper").get(0) ||
            $container.closest(".box-swiper").find(".group-btn-slider .nav-prev-swiper").get(0);

function formatNumber(num) {
    return String(num);
}

        function getTotalSlides() {
            var total = $container.find(".swiper-wrapper > .swiper-slide:not(.swiper-slide-duplicate)").length;

            if (!total) {
                total = $container.find(".swiper-wrapper > .swiper-slide").length;
            }

            return total;
        }

        function updateFraction(swiper) {
            if (!$fraction.length) {
                return;
            }

            var total = getTotalSlides();
            var current = swiper.realIndex !== undefined ? swiper.realIndex + 1 : swiper.activeIndex + 1;

            if (current > total) {
                current = total;
            }

            $fraction.html(
                '<span class="current">' + formatNumber(current) + '</span>' +
                '<span class="separator"> / </span>' +
                '<span class="total">' + formatNumber(total) + '</span>'
            );
        }

        var opts = {
            direction: direction,
            speed: speed,
            slidesPerView: mobile,
            spaceBetween: spacing,
            slidesPerGroup: perGroup,
            grabCursor: cursorType,
            loop: loop,
            initialSlide: initSlide,
            effect: effect,
            // QA UX-027 (WCAG 2.2.2): no auto-moving slides for people who asked the OS for reduced motion.
            autoplay: (atPlay && !(window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches)) ? {
                delay: delay,
                disableOnInteraction: false,
                pauseOnMouseEnter: pauseHover
            } : false,
            observer: true,
            observeParents: true,
            watchOverflow: false,
            breakpoints: {
                575: {
                    slidesPerView: mobileSm,
                    spaceBetween: spacingSm,
                    slidesPerGroup: perGroupSm,
                    grid: {
                        rows: gridRowsMobile,
                        fill: "row"
                    }
                },
                768: {
                    slidesPerView: tablet,
                    spaceBetween: spacingMd,
                    slidesPerGroup: perGroupMd,
                    grid: {
                        rows: gridRows,
                        fill: "row"
                    }
                },
                1200: {
                    slidesPerView: preview,
                    spaceBetween: spacingLg,
                    slidesPerGroup: perGroupLg,
                    grid: {
                        rows: gridRows,
                        fill: "row"
                    }
                }
            },
            centeredSlides: centerMode,
            on: {
                init: function () {
                    updateFraction(this);
                },
                slideChange: function () {
                    updateFraction(this);
                },
                realIndexChange: function () {
                    updateFraction(this);
                },
                resize: function () {
                    updateFraction(this);
                }
            }
        };

        if (laptop !== undefined) {
            opts.breakpoints[1440] = {
                slidesPerView: laptop,
                spaceBetween: spacingXl,
                slidesPerGroup: perGroupXl,
                centeredSlides: centerMode,
                grid: {
                    rows: gridRows,
                    fill: "row"
                }
            };
        }

        if (gridRowsMobile && gridRowsMobile > 1) {
            opts.grid = {
                rows: gridRowsMobile,
                fill: "row"
            };
        }

        if ($pagination.length) {
            opts.pagination = {
                el: $pagination.get(0),
                clickable: true
            };
        }

        if (nextEl || prevEl) {
            opts.navigation = {
                nextEl: nextEl || null,
                prevEl: prevEl || null
            };
        }

        var swiperT = new Swiper(element, opts);

        // QA UX-027: pause autoplay while keyboard focus is inside the slider, resume when it leaves.
        if (swiperT.autoplay && swiperT.autoplay.running) {
            $container
                .off("focusin.tfAuto focusout.tfAuto")
                .on("focusin.tfAuto", function () {
                    swiperT.autoplay.stop();
                })
                .on("focusout.tfAuto", function (e) {
                    if (!e.relatedTarget || !$container.get(0).contains(e.relatedTarget)) {
                        swiperT.autoplay.start();
                    }
                });
        }

        updateFraction(swiperT);

        $container.find(".swiper-button")
            .off(".tfsw")
            .on("mouseenter.tfsw", function () {
                var slideIndex = $(this).data("slide");

                if (typeof slideIndex !== "undefined") {
                    swiperT.slideTo(slideIndex, 500, false);
                    updateFraction(swiperT);
                }

                $container.find(".card_product--V01.style_2")
                    .removeClass("active")
                    .eq(slideIndex)
                    .addClass("active");
            })
            .on("mouseleave.tfsw", function () {
                $container.find(".card_product--V01.style_2").removeClass("active");
            })
            .on("click.tfsw", function () {
                var slideIndex = $(this).data("slide");

                $container.find(".card_product--V01.style_2")
                    .eq(slideIndex)
                    .toggleClass("clicked");
            });
    });
}

    // "Ovinx Banner Feature" widget's `.tf-sw-thumbs` pairs a thumbnail swiper
    // (`.sw-thumb`) with a synced main-content swiper (`.sw-main-thumb`), each
    // controlling the other via Swiper's `controller.control`. Ground truth's
    // own `carousel.js` has this exact pattern, but wired with bare global
    // selectors (`$(".sw-thumb")`) run once on document ready — that can't be
    // re-initialized per Elementor widget instance (needed for the editor's
    // live preview, and to avoid double-binding if the widget is ever used
    // more than once on a page), so this is a scoped rebuild of it instead.
    function initThumbSwiperInScope($scope) {
        var $wrap = $scope.find(".tf-sw-thumbs").first();

        if (!$wrap.length) {
            return;
        }

        var thumbEl = $wrap.find(".sw-thumb").get(0);
        var mainEl = $wrap.find(".sw-main-thumb").get(0);

        if (!thumbEl || !mainEl) {
            return;
        }

        if (thumbEl.swiper && typeof thumbEl.swiper.destroy === "function") {
            thumbEl.swiper.destroy(true, true);
        }
        if (mainEl.swiper && typeof mainEl.swiper.destroy === "function") {
            mainEl.swiper.destroy(true, true);
        }

        var thumbSwiper = new Swiper(thumbEl, {
            watchSlidesProgress: true,
            speed: 800,
            slidesPerView: 1,
            centeredSlides: true,
            slideToClickedSlide: true
        });

        var $pagination = $wrap.find(".tf-sw-pagination").first();
        var nextEl = $wrap.find(".nav-next-swiper").get(0);
        var prevEl = $wrap.find(".nav-prev-swiper").get(0);

        var mainSwiper = new Swiper(mainEl, {
            grabCursor: true,
            speed: 800,
            observer: true,
            observeParents: true,
            navigation: (nextEl || prevEl) ? {
                nextEl: nextEl || null,
                prevEl: prevEl || null
            } : undefined,
            pagination: $pagination.length ? {
                el: $pagination.get(0),
                clickable: true
            } : false
        });

        thumbSwiper.controller.control = mainSwiper;
        mainSwiper.controller.control = thumbSwiper;
    }

    // QA UX-020 / UX-025: keyboard + screen-reader support for the `.nice-select` vehicle selectors (Check Fit,
    // Slideshow Product Search Filter). The dropdown was mouse-only: only the invisible `ul.list` took a Tab stop and
    // no key did anything. Now the `.nice-select` itself is the combobox (Tab / Enter / Space / Arrows / Esc / Home / End)
    // and its `ul.list` the listbox. Mouse behaviour is unchanged — keys just reuse the existing click handlers.
    function enhanceNiceSelectA11y($niceSelects) {
        $niceSelects.each(function () {
            var $nice = $(this);
            var $field = $nice.closest(".field-select");

            if ($nice.data("tf-a11y")) {
                return;
            }
            $nice.data("tf-a11y", true);

            var label = $nice.data("label") || $.trim($nice.find(".current").first().text());

            $nice.attr({
                role: "combobox",
                "aria-haspopup": "listbox",
                "aria-expanded": "false",
                "aria-label": label
            });
            $nice.find(".list").attr("role", "listbox");
            $nice.find(".option").attr("role", "option");

            function syncDisabled() {
                var disabled = $field.hasClass("is-disable");
                $nice.attr({ tabindex: disabled ? "-1" : "0", "aria-disabled": disabled ? "true" : "false" });
            }
            syncDisabled();

            if (window.MutationObserver) {
                new MutationObserver(function () {
                    $nice.attr("aria-expanded", $nice.hasClass("open") ? "true" : "false");
                    if (!$nice.hasClass("open")) {
                        $nice.find(".option.focused").removeClass("focused");
                    }
                }).observe($nice.get(0), { attributes: true, attributeFilter: ["class"] });

                new MutationObserver(syncDisabled).observe($field.get(0), { attributes: true, attributeFilter: ["class"] });
            }
        });

        $(document).off("keydown.tfNiceKb").on("keydown.tfNiceKb", ".field-select .nice-select", function (e) {
            var $nice = $(this);

            if ($nice.closest(".field-select").hasClass("is-disable")) {
                return;
            }

            var key = e.key;
            var isOpen = $nice.hasClass("open");
            var $options = $nice.find(".option:not(.disabled)");
            var $focused = $options.filter(".focused");

            function focusOption($opt) {
                $options.removeClass("focused");
                if ($opt.length) {
                    $opt.addClass("focused");
                    var list = $nice.find(".list").get(0);
                    var el = $opt.get(0);
                    if (list && el) {
                        if (el.offsetTop < list.scrollTop) {
                            list.scrollTop = el.offsetTop;
                        } else if (el.offsetTop + el.offsetHeight > list.scrollTop + list.clientHeight) {
                            list.scrollTop = el.offsetTop + el.offsetHeight - list.clientHeight;
                        }
                    }
                }
            }

            if (key === "ArrowDown" || key === "ArrowUp") {
                e.preventDefault();
                if (!isOpen) {
                    $nice.trigger("click");
                    focusOption($options.filter(".selected").first().length ? $options.filter(".selected").first() : $options.first());
                    return;
                }
                var idx = $options.index($focused);
                idx = key === "ArrowDown" ? Math.min(idx + 1, $options.length - 1) : Math.max(idx - 1, 0);
                focusOption($options.eq(idx));
            } else if (key === "Home" || key === "End") {
                if (isOpen) {
                    e.preventDefault();
                    focusOption(key === "Home" ? $options.first() : $options.last());
                }
            } else if (key === "Enter" || key === " ") {
                e.preventDefault();
                if (isOpen && $focused.length) {
                    $focused.trigger("click");
                } else {
                    $nice.trigger("click");
                }
            } else if (key === "Escape") {
                if (isOpen) {
                    e.preventDefault();
                    e.stopPropagation();
                    $nice.removeClass("open");
                }
            } else if (key === "Tab" && isOpen) {
                $nice.removeClass("open");
            }
        });
    }
    // "Ovinx Slideshow" widget's Product Search Filter panel renders static
    // `.field-select .nice-select` markup server-side (real term data, no live
    // WooCommerce filtering behind it yet). The theme's own nice-select.js only
    // binds its document-wide click handlers when a real `select.st-select`
    // exists on the page (see main.js `styleSelect()`), which never happens
    // here — so this dropdown needs its own minimal open/select toggle,
    // scoped to `.field-select` only (does not touch any other real
    // `select.st-select` elsewhere on the site).
    function initNiceSelectInScope($scope) {
        // Cascading filters (`.is-cascade`) are driven by initVehicleCascadeInScope().
        var $niceSelects = $scope.find(".field-select .nice-select").not(".is-cascade .nice-select");

        if (!$niceSelects.length) {
            return;
        }

        enhanceNiceSelectA11y($niceSelects);

        $niceSelects.each(function () {
            var $this = $(this);

            if ($this.data("tf-nice-select-bound")) {
                return;
            }
            $this.data("tf-nice-select-bound", true);

            $this.on("click", function (e) {
                e.stopPropagation();
                var isOpen = $this.hasClass("open");
                $(".field-select .nice-select").not($this).removeClass("open");
                $this.toggleClass("open", !isOpen);
            });

            $this.find(".option:not(.disabled)").on("click", function (e) {
                // Stop this from bubbling up to the `.nice-select` click handler
                // above — without this, that handler re-toggles `open` back on
                // right after we just closed it (both handlers fire on the
                // same click since `.option` is nested inside `.nice-select`).
                e.stopPropagation();

                var $option = $(this);

                $this.find(".selected").removeClass("selected");
                $option.addClass("selected");
                $this.find(".current").text($option.text().trim());
                $this.removeClass("open");
            });
        });

        $(document)
            .off("click.tfNiceSelect")
            .on("click.tfNiceSelect", function (e) {
                if (!$(e.target).closest(".field-select .nice-select").length) {
                    $(".field-select .nice-select").removeClass("open");
                }
            });
    }

    // "Ovinx Check Fit" cascading filter (`.filter-vehicle.is-cascade`): one term
    // tree (JSON in `.tf-vehicle-tree`, built server-side from a single
    // category/brand/tag/attribute source) drives every dropdown — select 1
    // lists the roots, picking a term fills + unlocks the next select with that
    // term's children, and re-picking an earlier select resets all later ones.
    // The submit button enables once select 1 has a value; all selects share one
    // `data-query-var`, so initSearchFilterSubmit() ends up sending the deepest
    // selected term (WooCommerce's tax_query includes its children).
    function initVehicleCascadeInScope($scope) {
        $scope.find(".filter-vehicle.is-cascade").each(function () {
            var $root = $(this);

            if ($root.data("tf-cascade-bound")) {
                return;
            }
            $root.data("tf-cascade-bound", true);

            var tree = [];
            try {
                tree = JSON.parse($root.find(".tf-vehicle-tree").first().text() || "[]");
            } catch (err) {
                tree = [];
            }

            var $fields = $root.find(".field-select");
            var $button = $root.find(".btn-action_submit");

            enhanceNiceSelectA11y($root.find(".field-select .nice-select"));
            var selected = [];

            function nodesFor(level) {
                return level === 0 ? tree : (selected[level - 1] && selected[level - 1].c) || [];
            }

            function resetField(level) {
                var nodes = nodesFor(level);
                var $field = $fields.eq(level);
                var $nice = $field.find(".nice-select");
                var $list = $nice.find(".list");

                $list.children(".option:not(.option-all)").remove();
                $.each(nodes, function (i, node) {
                    $("<li/>", { "class": "option", "role": "option", "data-index": i, "data-value": node.v, text: node.n }).appendTo($list);
                });

                $nice.removeClass("open").find(".current").text($nice.data("label"));
                $field.removeClass("is-select").toggleClass("is-disable", !nodes.length);
            }

            $root.on("click", ".field-select .nice-select", function (e) {
                e.stopPropagation();
                var $nice = $(this);
                var isOpen = $nice.hasClass("open");
                $root.find(".nice-select").removeClass("open");
                $nice.toggleClass("open", !isOpen);
            });

            $root.on("click", ".field-select .option:not(.disabled)", function (e) {
                e.stopPropagation();

                var $option = $(this);
                var $field = $option.closest(".field-select");
                var $nice = $field.find(".nice-select");
                var level = $fields.index($field);

                selected = selected.slice(0, level);
                selected[level] = nodesFor(level)[$option.data("index")];

                $nice.find(".selected").removeClass("selected");
                $option.addClass("selected");
                $nice.find(".current").text($option.text().trim());
                $nice.removeClass("open");
                $field.addClass("is-select");

                for (var next = level + 1; next < $fields.length; next++) {
                    resetField(next);
                }

                $button.prop("disabled", !selected[0]);
            });

            $(document).on("click.tfVehicleCascade", function (e) {
                if (!$(e.target).closest($root).length) {
                    $root.find(".nice-select").removeClass("open");
                }
            });
        });
    }

    // "Search Parts" button: read the selected term slug of every
    // `.field-select .nice-select[data-query-var]` inside the same widget,
    // then navigate to the real Shop page with those as GET params — handled
    // server-side by `TFWC_Elementor_Widget_Addon::filter_shop_query_from_search_widget()`
    // (`pre_get_posts` on the Shop main query), so this actually filters
    // products, not just a decorative redirect.
    function initSearchFilterSubmit($scope) {
        var $buttons = $scope.find(".btn-action_submit[data-shop-url]");

        if (!$buttons.length) {
            return;
        }

        $buttons.each(function () {
            var $button = $(this);

            if ($button.data("tf-search-submit-bound")) {
                return;
            }
            $button.data("tf-search-submit-bound", true);

            $button.on("click", function () {
                var shopUrl = $button.data("shop-url");

                if (!shopUrl) {
                    return;
                }

                var params = {};

                $button
                    .closest(".filter-vehicle")
                    .find(".field-select .nice-select[data-query-var]")
                    .each(function () {
                        var $niceSelect = $(this);
                        var queryVar = $niceSelect.data("query-var");
                        var value = $niceSelect.find(".option.selected").data("value");

                        if (queryVar && value) {
                            params[queryVar] = value;
                        }
                    });

                var query = $.param(params);
                window.location.href = shopUrl + (query ? "?" + query : "");
            });
        });
    }

    // "Ovinx Product Tabs" widget (2026-09-28): only the FIRST tab's Swiper
    // is rendered/initialized on page load — every other `.tab-pane` starts
    // as an empty placeholder (loading spinner) carrying its query args in
    // `data-tf-tab-query` (see `widget-product-tabs.php::render()`), and its
    // real product markup is fetched via `wp_ajax_ovinx_load_product_tab`
    // only the first time that tab is actually shown — see
    // `initProductTabsLazyLoadInScope()` below. Only ONE shared prev/next nav
    // pair exists (in the heading, outside `.tab-content`).
    // `initSwiperInScope`'s own nav lookup already falls back to
    // `.closest(".tf-btn-swiper-main")`, so EVERY tab's swiper binds
    // to that same physical pair of buttons — left alone, clicking "next"
    // would advance every tab's swiper simultaneously, not just the visible
    // one. This disables navigation on every inactive tab's swiper (Swiper's
    // own `navigation.enable()/.disable()` API), leaving only the active
    // tab's swiper listening, and swaps that on Bootstrap's `shown.bs.tab`.
    // Also calls `.update()` on the newly-shown tab's swiper, since a Swiper
    // initialized while its ancestor `.tab-pane` was `display:none` measures
    // 0-width slides — `observer`/`observeParents` alone don't reliably catch
    // a `display:none → block` transition from a class toggle.
    function initProductTabsNavInScope($scope) {
        var $wrap = $scope.find(".section-product-tab.tf-btn-swiper-main").first();

        if (!$wrap.length) {
            return;
        }

        function getPaneSwiper($pane) {
            var el = $pane.find(".tfc-swiper").get(0);
            return el && el.swiper ? el.swiper : null;
        }

        $wrap.find(".tab-pane").each(function () {
            var $pane = $(this);
            var swiperInstance = getPaneSwiper($pane);

            if (!swiperInstance || !swiperInstance.navigation) {
                return;
            }

            if ($pane.hasClass("active")) {
                swiperInstance.navigation.enable();
            } else {
                swiperInstance.navigation.disable();
            }
        });

        $wrap.find('[data-bs-toggle="tab"]')
            .off("shown.bs.tab.tfProductTabs")
            .on("shown.bs.tab.tfProductTabs", function (e) {
                var $prevTrigger = e.relatedTarget ? $(e.relatedTarget) : null;

                if ($prevTrigger && $prevTrigger.length) {
                    var $prevPane = $($prevTrigger.attr("href"));
                    var prevSwiper = getPaneSwiper($prevPane);

                    if (prevSwiper && prevSwiper.navigation) {
                        prevSwiper.navigation.disable();
                    }
                }

                var $targetPane = $($(e.target).attr("href"));
                var targetSwiper = getPaneSwiper($targetPane);

                if (targetSwiper) {
                    if (targetSwiper.navigation) {
                        targetSwiper.navigation.enable();
                    }
                    targetSwiper.update();
                }
            });
    }

    // Fires the actual AJAX fetch for a not-yet-loaded `.tab-pane` — either
    // pre-emptively on hover/focus of its trigger (so a mouse user's click
    // usually lands on already-loaded content), or as a fallback the moment
    // it's actually shown (touch, keyboard-without-focus-fetch, or the fetch
    // just hasn't resolved yet). Reuses the theme's own already-localized
    // `tfwc_woo_params` (ajax_url + nonce for the `tfwc_nonce` action)
    // instead of localizing a widget-specific script — same object every
    // other WooCommerce AJAX call in this theme already uses, and it's
    // enqueued site-wide.
    //
    // The swap itself crossfades (`.tf-tab-pane-fade`/`-fade-out`, CSS in
    // `widget-product-tabs.php::print_skeleton_css_once()`) instead of a
    // hard `.html()` replace, and the placeholder it's replacing is a
    // skeleton grid sized like the real card grid (not a small centered
    // spinner) — together these keep the swap from reading as a layout jump.
    function initProductTabsLazyLoadInScope($scope) {
        var $wrap = $scope.find(".section-product-tab.tf-btn-swiper-main").first();

        if (!$wrap.length || typeof tfwc_woo_params === "undefined") {
            return;
        }

        function setPaneNavState($pane) {
            var el = $pane.find(".tfc-swiper").get(0);

            if (!el || !el.swiper || !el.swiper.navigation) {
                return;
            }

            if ($pane.hasClass("active")) {
                el.swiper.navigation.enable();
            } else {
                el.swiper.navigation.disable();
            }
        }

        function loadPane($pane) {
            var query = $pane.attr("data-tf-tab-query");

            if (!query || $pane.data("tf-tab-loading")) {
                return;
            }

            var params;
            try {
                params = JSON.parse(query);
            } catch (e) {
                return;
            }

            $pane.data("tf-tab-loading", true);

            $.post(
                tfwc_woo_params.ajax_url,
                $.extend({}, params, {
                    action: "ovinx_load_product_tab",
                    nonce: tfwc_woo_params.nonce
                })
            )
                .done(function (response) {
                    if (!response || !response.success || !response.data) {
                        return;
                    }

                    $pane.removeAttr("data-tf-tab-query");

                    var html = response.data.html
                        ? response.data.html
                        : '<p class="text-center py-5">' + (tfwc_woo_params.i18n_no_products || "No products found.") + "</p>";

                    // Swap right away (no timer) and fade the new content in. The old
                    // approach faded the pane itself, but `.tab-pane.active{opacity:1}`
                    // out-ranks `.tf-tab-pane-fade-out{opacity:0}`, so it never faded —
                    // it just waited 200ms and then popped in. Hold the skeleton's
                    // height until the swap so the section doesn't collapse mid-swap.
                    var paneHeight = $pane.outerHeight();

                    $pane.css("min-height", paneHeight);
                    $pane.html(html);
                    $pane.children().addClass("tf-tab-pane-in");
                    initSwiperInScope($pane);
                    setPaneNavState($pane);

                    setTimeout(function () {
                        $pane.css("min-height", "");
                    }, 320);
                })
                .always(function () {
                    $pane.removeData("tf-tab-loading");
                });
        }

        $wrap.find('[data-bs-toggle="tab"]')
            .off("shown.bs.tab.tfProductTabsLazy mouseenter.tfProductTabsLazy focus.tfProductTabsLazy")
            .on("shown.bs.tab.tfProductTabsLazy", function (e) {
                loadPane($($(e.target).attr("href")));
            })
            .on("mouseenter.tfProductTabsLazy focus.tfProductTabsLazy", function () {
                loadPane($($(this).attr("href")));
            });
    }

    // "Ovinx Product Tab Grid" widget (2026-09-28): same lazy-load-past-
    // first-tab pattern as initProductTabsLazyLoadInScope() above, adapted
    // for a plain CSS grid instead of a swiper — no swiper to re-init/enable
    // nav on after the swap, just an html() replace. Scoped to
    // `.tf-product-tab-grid` (this widget's own wrapper class, distinct from
    // `.section-product-tab.tf-btn-swiper-main`) and its own AJAX action
    // (`ovinx_load_product_tab_grid`) / event namespace
    // (`tfProductTabGridLazy`) so the two widgets never cross-trigger each
    // other's tabs even when both are on the same page.
    function initProductTabGridLazyLoadInScope($scope) {
        var $wrap = $scope.find(".tf-product-tab-grid").first();

        if (!$wrap.length || typeof tfwc_woo_params === "undefined") {
            return;
        }

        function loadPane($pane) {
            var query = $pane.attr("data-tf-tab-query");

            if (!query || $pane.data("tf-tab-loading")) {
                return;
            }

            var params;
            try {
                params = JSON.parse(query);
            } catch (e) {
                return;
            }

            $pane.data("tf-tab-loading", true);

            $.post(
                tfwc_woo_params.ajax_url,
                $.extend({}, params, {
                    action: "ovinx_load_product_tab_grid",
                    nonce: tfwc_woo_params.nonce
                })
            )
                .done(function (response) {
                    if (!response || !response.success || !response.data) {
                        return;
                    }

                    $pane.removeAttr("data-tf-tab-query");

                    var html = response.data.html
                        ? response.data.html
                        : '<p class="text-center py-5">' + (tfwc_woo_params.i18n_no_products || "No products found.") + "</p>";

                    // Swap right away and fade the new content in (see the same
                    // fix in initProductTabsLazyLoadInScope above): the old
                    // fade-out class never took effect on the active pane.
                    $pane.css("min-height", $pane.outerHeight());
                    $pane.html(html);
                    $pane.children().addClass("tf-tab-pane-in");

                    setTimeout(function () {
                        $pane.css("min-height", "");
                    }, 320);
                })
                .always(function () {
                    $pane.removeData("tf-tab-loading");
                });
        }

        $wrap.find('[data-bs-toggle="tab"]')
            .off("shown.bs.tab.tfProductTabGridLazy mouseenter.tfProductTabGridLazy focus.tfProductTabGridLazy")
            .on("shown.bs.tab.tfProductTabGridLazy", function (e) {
                loadPane($($(e.target).attr("href")));
            })
            .on("mouseenter.tfProductTabGridLazy focus.tfProductTabGridLazy", function () {
                loadPane($($(this).attr("href")));
            });
    }

    // "Ovinx Banner Sale Coupon" widget: copies the real WooCommerce coupon
    // code (rendered server-side in `data-coupon-code`, not read back from
    // `.coppyText`'s text so this doesn't depend on its `text-uppercase`
    // CSS) to the clipboard on click. Same `$.toast()` success convention
    // already used site-wide (see `themes/ovinx/js/shop.js`'s
    // `#btn-coppy-text` handler, ~line 1337) instead of ground truth's
    // `main.js` `textCopy()`, which uses `alert()` — kept off here for a
    // consistent toast UX. Falls back to `document.execCommand('copy')`
    // for non-secure contexts, same as that shop.js handler.
    function initCouponCopyInScope($scope) {
        $scope.find('.coupon-copy-wrap')
            .off('click.tfCouponCopy')
            .on('click.tfCouponCopy', function (e) {
                e.stopPropagation();

                var $button = $(this);
                var code = $button.data('coupon-code');

                if (!code) {
                    return;
                }

                code = String(code);

                function showSuccessToast() {
                    if (typeof $.toast === 'function') {
                        $.toast({
                            heading: 'Success',
                            text: 'Coupon code copied',
                            showHideTransition: 'slide',
                            icon: 'success',
                            hideAfter: 1500
                        });
                    }
                }

                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(code).then(showSuccessToast).catch(function (err) {
                        console.error('Failed to copy coupon code: ' + err);
                    });
                } else {
                    try {
                        var $temp = $('<textarea readonly></textarea>')
                            .css({ position: 'fixed', top: '-9999px', left: '-9999px' })
                            .val(code)
                            .appendTo('body');

                        $temp.get(0).select();
                        document.execCommand('copy');
                        $temp.remove();

                        showSuccessToast();
                    } catch (err) {
                        console.log('Copy failed', err);
                    }
                }
            });
    }

    $(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction("frontend/element_ready/ovinx_slideshow.default", initSwiperInScope);
        elementorFrontend.hooks.addAction("frontend/element_ready/ovinx_slideshow.default", initNiceSelectInScope);
        elementorFrontend.hooks.addAction("frontend/element_ready/ovinx_slideshow.default", initVehicleCascadeInScope);
        elementorFrontend.hooks.addAction("frontend/element_ready/ovinx_slideshow.default", initSearchFilterSubmit);
        elementorFrontend.hooks.addAction("frontend/element_ready/ovinx_check_fit.default", initVehicleCascadeInScope);
        elementorFrontend.hooks.addAction("frontend/element_ready/ovinx_check_fit.default", initSearchFilterSubmit);
        elementorFrontend.hooks.addAction("frontend/element_ready/ovinx_category_product.default", initSwiperInScope);
        elementorFrontend.hooks.addAction("frontend/element_ready/ovinx_banner_feature.default", initThumbSwiperInScope);
        elementorFrontend.hooks.addAction("frontend/element_ready/ovinx_blog.default", initSwiperInScope);
        elementorFrontend.hooks.addAction("frontend/element_ready/ovinx_testimonial.default", initSwiperInScope);
        elementorFrontend.hooks.addAction("frontend/element_ready/ovinx_product_tabs.default", initSwiperInScope);
        elementorFrontend.hooks.addAction("frontend/element_ready/ovinx_product_tabs.default", initProductTabsNavInScope);
        elementorFrontend.hooks.addAction("frontend/element_ready/ovinx_product_tabs.default", initProductTabsLazyLoadInScope);
        elementorFrontend.hooks.addAction("frontend/element_ready/ovinx_product_tab_grid.default", initProductTabGridLazyLoadInScope);
        elementorFrontend.hooks.addAction("frontend/element_ready/ovinx_product_carousel.default", initSwiperInScope);
        elementorFrontend.hooks.addAction("frontend/element_ready/ovinx_product_list_carousel.default", initSwiperInScope);
        elementorFrontend.hooks.addAction("frontend/element_ready/ovinx_banner_sale_coupon.default", initCouponCopyInScope);
        elementorFrontend.hooks.addAction("frontend/element_ready/ovinx_product_showcase_slider.default", initSwiperInScope);
        elementorFrontend.hooks.addAction("frontend/element_ready/ovinx_icon_list.default", initSwiperInScope);
        initSwiperInScope($(document));
        initNiceSelectInScope($(document));
        initVehicleCascadeInScope($(document));
        initSearchFilterSubmit($(document));
    });

})(jQuery);
