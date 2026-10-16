jQuery(document).ready(function ($) {
    function initializeSelect2($select, model) {
        
        let ajaxAction = 'tfwc_select2_options';
        let selectedValues = model.get('settings').get($select.data('setting'));
        if( !selectedValues){
            selectedValues = $select.data("value");
        }

        let valuesArray = [];
        // let valuesArray = Array.isArray(selectedValues) ? selectedValues : [selectedValues];
        if (Array.isArray(selectedValues)) {
            valuesArray = selectedValues.map(String);
        } else if (typeof selectedValues === "string" && selectedValues.trim() !== "") {
            valuesArray = selectedValues.split(",").map(val => val.trim());
        } else if (typeof selectedValues === "number") {
            valuesArray = [String(selectedValues)];
        }

        $.ajax({
            url: tfwcSelect2Ajax.ajax_url,
            dataType: 'json',
            data: {
                action: ajaxAction,
                security: tfwcSelect2Ajax.nonce,
                selected: valuesArray
            },
            success: function (data) {
                if (!$select.hasClass('select2-hidden-accessible')) {
                    $select.select2({
                        ajax: {
                            url: tfwcSelect2Ajax.ajax_url,
                            dataType: 'json',
                            delay: 250,
                            data: function (params) {
                                return {
                                    q: params.term,
                                    action: ajaxAction,
                                    security: tfwcSelect2Ajax.nonce
                                };
                            },
                            processResults: function (data) {
                                return { results: data };
                            },
                            cache: true
                        },
                        minimumInputLength: 2,
                        placeholder: $select.attr('data-placeholder') || 'Search...',
                        allowClear: true,
                        data: valuesArray.length > 0 ? data : null
                    });
                    if (valuesArray.length > 0) {
                        $select.val(valuesArray).trigger('change');
                    }
                    $select.on('select2:unselect', function (e) {
                        const unselectedId = e.params.data.id;
                        $select.find('option[value="' + unselectedId + '"]').remove();
                    });
                }
            }
        });
    }

    function observeElementorControls() {
        const observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                if (mutation.type === 'childList') {
                    $('.elementor-tfwc-select2', $(mutation.addedNodes) ).each(function () {
                        let model =  elementor.getPanelView().currentPageView.model;
                        initializeSelect2($(this), model);
                    });
                }
            });
        });
        observer.observe(document.querySelector('.elementor-panel'), { childList: true, subtree: true });
    }

    observeElementorControls();

});