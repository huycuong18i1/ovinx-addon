(function ($) {
    "use strict";

    function Util() {}

    Util.setAttributes = function (el, attrs) {
        for (var key in attrs) {
            if (Object.prototype.hasOwnProperty.call(attrs, key)) {
                el.setAttribute(key, attrs[key]);
            }
        }
    };

    var CountDown = function (element) {
        this.element = element;
        this.timeoutId = null;
        this.destroyed = false;

        if (!this.element) {
            return;
        }

        if (this.element._ovinxCountdown) {
            this.element._ovinxCountdown.destroy();
        }

        this.element._ovinxCountdown = this;

        this.labels = this.element.getAttribute("data-labels")
            ? this.element.getAttribute("data-labels").split(",")
            : ["Days", "Hours", "Mins", "Secs"];

        if (!this.labels || this.labels.length < 4) {
            this.labels = ["Days", "Hours", "Mins", "Secs"];
        }

        this.setVisibleLabels();
        this.removeOldCountDown();
        this.createCountDown();

        this.days  = this.element.getElementsByClassName("js-countdown__value--0")[0];
        this.hours = this.element.getElementsByClassName("js-countdown__value--1")[0];
        this.mins  = this.element.getElementsByClassName("js-countdown__value--2")[0];
        this.secs  = this.element.getElementsByClassName("js-countdown__value--3")[0];

        this.endTime = this.getEndTime();

        this.initCountDown();
    };

    CountDown.prototype.destroy = function () {
        this.destroyed = true;

        if (this.timeoutId) {
            clearTimeout(this.timeoutId);
            this.timeoutId = null;
        }

        this.removeOldCountDown();

        if (this.element && this.element._ovinxCountdown === this) {
            delete this.element._ovinxCountdown;
        }
    };

    CountDown.prototype.removeOldCountDown = function () {
        if (!this.element) {
            return;
        }

        var oldTimer = this.element.querySelector(".countdown__timer");

        if (oldTimer && oldTimer.parentNode === this.element) {
            oldTimer.parentNode.removeChild(oldTimer);
        }
    };

    CountDown.prototype.setVisibleLabels = function () {
        this.visibleLabels = this.element.getAttribute("data-visible-labels")
            ? this.element.getAttribute("data-visible-labels").split(",")
            : [];

        this.visibleLabels = this.visibleLabels.map(function (label) {
            return label.trim();
        });
    };

    CountDown.prototype.createCountDown = function () {
        var wrapper = document.createElement("div");

        Util.setAttributes(wrapper, {
            "aria-hidden": "true",
            "class": "countdown__timer"
        });

var separator = this.element.getAttribute("data-separator") || "colon";

var hasCustom =
    separator === "colon" ||
    this.element.classList.contains("cd-custom");

var hasCustomElement =
    separator === "element" ||
    this.element.classList.contains("cd-custom-element");

        for (var i = 0; i < 4; i++) {
            var timeItem = document.createElement("span");
            var timeValue = document.createElement("span");
            var timeLabel = document.createElement("span");

            timeItem.setAttribute("class", "countdown__item");
            timeValue.setAttribute("class", "countdown__value countdown__value--" + i + " js-countdown__value--" + i);

            timeItem.appendChild(timeValue);

            if (this.labels && this.labels.length > 0) {
                timeLabel.textContent = this.labels[i] ? this.labels[i].trim() : "";
                timeLabel.setAttribute("class", "countdown__label");
                timeItem.appendChild(timeLabel);
            }

            wrapper.appendChild(timeItem);

            if (i < 3) {
                if (hasCustom) {
                    var timeCustom = document.createElement("span");
                    timeCustom.setAttribute("class", "countdown__custom");
                    timeCustom.textContent = ":";
                    wrapper.appendChild(timeCustom);
                }

                if (hasCustomElement) {
                    var customWrapper = document.createElement("span");
                    var innerSpan = document.createElement("span");

                    customWrapper.setAttribute("class", "countdown__custom--element");
                    innerSpan.setAttribute("class", "countdown__custom--css");

                    customWrapper.appendChild(innerSpan);
                    wrapper.appendChild(customWrapper);
                }
            }
        }

        this.element.insertBefore(wrapper, this.element.firstChild);
    };

    CountDown.prototype.parseDate = function (dateString) {
        if (!dateString) {
            return false;
        }

        dateString = dateString.trim();

        var timestamp = Date.parse(dateString);

        if (isNaN(timestamp)) {
            timestamp = Date.parse(dateString.replace(/-/g, "/"));
        }

        if (isNaN(timestamp)) {
            return false;
        }

        return timestamp;
    };

    CountDown.prototype.getEndTime = function () {
        var timer = this.element.getAttribute("data-timer");
        var countdown = this.element.getAttribute("data-countdown");

        if (timer) {
            return Number(timer) * 1000 + new Date().getTime();
        }

        if (countdown) {
            return this.parseDate(countdown);
        }

        return false;
    };

    CountDown.prototype.initCountDown = function () {
        if (!this.endTime || !this.days || !this.hours || !this.mins || !this.secs) {
            return;
        }

        this.updateCountDown();
    };

    CountDown.prototype.updateCountDown = function () {
        if (this.destroyed) {
            return;
        }

        var time = Math.floor((this.endTime - new Date().getTime()) / 1000);
        var days = 0;
        var hours = 0;
        var mins = 0;
        var seconds = 0;

        if (isNaN(time) || time < 0) {
            this.days.textContent = this.getTimeFormat(0, true);
            this.hours.textContent = this.getTimeFormat(0);
            this.mins.textContent = this.getTimeFormat(0);
            this.secs.textContent = this.getTimeFormat(0);

            this.emitEndEvent();
            return;
        }

        days = parseInt(time / 86400, 10);
        time = time % 86400;

        hours = parseInt(time / 3600, 10);
        time = time % 3600;

        mins = parseInt(time / 60, 10);
        time = time % 60;

        seconds = parseInt(time, 10);

        var labels = this.labels.slice();

        if (days === 1 && labels[0]) {
            labels[0] = labels[0].replace(/s$/, "");
        }

        if (hours === 1 && labels[1]) {
            labels[1] = labels[1].replace(/s$/, "");
        }

        if (mins === 1 && labels[2]) {
            labels[2] = labels[2].replace(/s$/, "");
        }

        if (seconds === 1 && labels[3]) {
            labels[3] = labels[3].replace(/s$/, "");
        }

        this.days.textContent = this.getTimeFormat(days, true);
        this.hours.textContent = this.getTimeFormat(hours);
        this.mins.textContent = this.getTimeFormat(mins);
        this.secs.textContent = this.getTimeFormat(seconds);

        this.updateLabels(labels);
        this.updateVisibility(days, hours);

        var self = this;

        if (this.timeoutId) {
            clearTimeout(this.timeoutId);
        }

        this.timeoutId = setTimeout(function () {
            self.updateCountDown();
        }, 1000);
    };

    CountDown.prototype.updateLabels = function (labels) {
        var countdownItems = this.element.getElementsByClassName("countdown__item");

        for (var i = 0; i < countdownItems.length; i++) {
            var labelEl = countdownItems[i].querySelector(".countdown__label");

            if (labelEl && labels[i]) {
                labelEl.textContent = labels[i];
            }
        }
    };

    CountDown.prototype.updateVisibility = function (days, hours) {
        if (!this.element.classList.contains("cd-no")) {
            return;
        }

        var countdownItems = this.element.getElementsByClassName("countdown__item");

        var hideDays = days === 0;
        var hideHours = hideDays && hours === 0;
        var visibilityMap = [!hideDays, !hideHours, true, true];

        for (var i = 0; i < countdownItems.length; i++) {
            var item = countdownItems[i];
            var visible = visibilityMap[i];

            item.style.display = visible ? "" : "none";

            var next = item.nextElementSibling;

            if (
                next &&
                (
                    next.classList.contains("countdown__custom") ||
                    next.classList.contains("countdown__custom--element")
                )
            ) {
                next.style.display = visible ? "" : "none";
            }
        }
    };

    CountDown.prototype.getTimeFormat = function (time, isDay) {
        var pad = this.element.classList.contains("cd-has-zero");

        if (isDay && pad && time === 0) {
            return "00";
        }

        return pad && time < 10 ? "0" + time : time.toString();
    };

    CountDown.prototype.emitEndEvent = function () {
        var event;

        if (typeof window.CustomEvent === "function") {
            event = new CustomEvent("countDownFinished");
        } else {
            event = document.createEvent("CustomEvent");
            event.initCustomEvent("countDownFinished", false, false, {});
        }

        this.element.dispatchEvent(event);
    };

    function initovinxCountdown($scope) {
        var $countdowns;

        if ($scope && $scope.length) {
            $countdowns = $scope.find(".js-countdown-core");

            if ($scope.hasClass("js-countdown-core")) {
                $countdowns = $countdowns.add($scope);
            }
        } else {
            $countdowns = $(".js-countdown-core");
        }

        if (!$countdowns.length) {
            return;
        }

        $countdowns.each(function () {
            new CountDown(this);
        });
    }

    $(window).on("elementor/frontend/init", function () {
        if (typeof elementorFrontend !== "undefined" && elementorFrontend.hooks) {
            elementorFrontend.hooks.addAction(
                "frontend/element_ready/ovinx_countdown.default",
                function ($scope) {
                    initovinxCountdown($scope);
                }
            );
            elementorFrontend.hooks.addAction(
                "frontend/element_ready/ovinx_product_with_banner.default",
                function ($scope) {
                    initovinxCountdown($scope);
                }
            );
        }
    });

    $(function () {
        initovinxCountdown();
    });

})(jQuery);