jQuery(function($) {

    var cart_button_selector = '.iwptp-button-cart_ajax, .iwptp-button-cart_redirect, .iwptp-button-cart_refresh, .iwptp-button-cart_checkout';

    // local cache
    window.iwptp_cache = {
        data: {},
        remove: function(url) {
            delete window.iwptp_cache.data[url];
        },
        exist: function(url) {
            return window.iwptp_cache.data.hasOwnProperty(url) && window.iwptp_cache.data[url] !== null;
        },
        get: function(url) {
            return window.iwptp_cache.data[url];
        },
        set: function(url, cachedData, callback) {
            window.iwptp_cache.remove(url);
            window.iwptp_cache.data[url] = cachedData;
            if ($.isFunction(callback)) callback(cachedData);
        }
    };

    window.iwptp_current_device = get_device();

    $(window).on('resize', function() {
        window.iwptp_cache.data = {};

        window.iwptp_previous_device = window.iwptp_current_device;
        window.iwptp_current_device = get_device();
        if (window.iwptp_previous_device !== window.iwptp_current_device) {
            $(window).trigger('iwptp_device_change', {
                previous_device: window.iwptp_previous_device,
                current_device: window.iwptp_current_device,
            })
        }
    });

    window.iwptp_product_form = {}; // product form cache

    function get_device() {
        // device
        var device = 'laptop'; // default

        if ($(window).width() <= iwptp_params.breakpoints.phone) {
            device = 'phone';
        } else if ($(window).width() <= iwptp_params.breakpoints.tablet) {
            device = 'tablet';
        }

        return device;
    }

    function get_device_table($iwptp) {
        var device = get_device(),
            table_selector = '.iwptp-table-scroll-wrapper-outer.iwptp-device-laptop:visible > .iwptp-table-scroll-wrapper > .iwptp-table, .iwptp-table-scroll-wrapper-outer.iwptp-device-laptop:visible .frzTbl-table';

        // if table for device not available, get table for next larger device
        if (device == 'phone' && !$iwptp.find(table_selector.replace('laptop', 'phone')).length) {
            device = 'tablet';
        }

        if (device == 'tablet' && !$iwptp.find(table_selector.replace('laptop', 'tablet')).length) {
            device = 'laptop';
        }

        var $table = $iwptp.find(table_selector.replace('laptop', device));

        return $table;
    }

    // html entity encode
    function htmlentity(string) {
        return string.replace(/[\u00A0-\u9999<>\&]/gim, function(i) {
            return '&#' + i.charCodeAt(0) + ';';
        });
    }

    // maintain scroll left upon column header sorting 
    $('body').on('click', '.frzTbl .iwptp-heading.iwptp-sortable', function() {
        var $this = $(this),
            $container = $this.closest('.iwptp'),
            $scrollOverlay = $this.closest('.frzTbl-content-wrapper').siblings('.frzTbl-scroll-overlay'),
            scrollLeft = $scrollOverlay[0].scrollLeft;

        $('body').one('after_freeze_table_build', '#' + $container.attr('id') + ' .frzTbl-table', function(e, frzTbl) {
            frzTbl.el.$scrollOverlay[0].scrollLeft = scrollLeft;
        })
    })

    // layout handler
    $('body').on('iwptp_layout', '.iwptp', function layout(e, data) {
        var $iwptp = $(this),
            $wrap = $iwptp.find('.iwptp-table-scroll-wrapper:visible'),
            $table = $wrap.find('.iwptp-table'),
            id = $iwptp.attr('data-iwptp-table-id');

        if ($('>.iwptp-device-view-loading-icon', $wrap).length) {
            var url = window.location.href,
                hash = window.location.hash,
                query_exists = url.indexOf('?') !== -1,
                query = '',
                device = get_device();

            if (hash) {
                url = url.replace(hash, '');
            }

            if (query_exists) {
                var replace = "&*" + id + "_device=(laptop|phone|tablet)";
                re = new RegExp(replace, "gm");

                url = (url.replace(re, "") + '&' + id + '_device=' + device).replace('?&', '?');

            } else {
                url = url + '?' + id + '_device=' + device;
            }

            if (hash) {
                url = url + hash;
            }

            // archive oveerride + compatible nav filter plugin = requires page refresh
            if ($iwptp.attr('data-iwptp-sc-attrs').indexOf('_only_loop') !== -1) {
                window.location = url;
                return;
            }

            query = url.substr(url.indexOf('?') + 1);

            if (hash) {
                query = query.replace(hash, '');
            }

            attempt_ajax($iwptp, query, false, 'device_view');

            return; // layout on AJAX response
        }

        // add pointer on sortable headings
        $wrap.find('.iwptp-heading').each(function() {
            var $this = $(this);
            if ($this.find('.iwptp-sorting-icons').length) {
                $this.addClass('iwptp-sortable');
            }
        })

        // freeze table
        var sc_attrs_string = $iwptp.attr('data-iwptp-sc-attrs'),
            sc_attrs = (sc_attrs_string && sc_attrs_string !== '{}') ? JSON.parse(sc_attrs_string) : {},
            options = {
                left: !sc_attrs.laptop_freeze_left ? 0 : parseInt(sc_attrs.laptop_freeze_left),
                right: !sc_attrs.laptop_freeze_right ? 0 : parseInt(sc_attrs.laptop_freeze_right),
                heading: !!sc_attrs.laptop_freeze_heading && sc_attrs.laptop_freeze_heading !== 'false',

                grab_and_scroll: !!sc_attrs.grab_and_scroll,

                wrapperWidth: !sc_attrs.laptop_freeze_wrapper_width ? 0 : parseInt(sc_attrs.laptop_freeze_wrapper_width),
                wrapperHeight: !sc_attrs.laptop_freeze_wrapper_height ? 0 : parseInt(sc_attrs.laptop_freeze_wrapper_height),

                tableWidth: !sc_attrs.laptop_freeze_table_width ? 0 : parseInt(sc_attrs.laptop_freeze_table_width),

                offset: !sc_attrs.laptop_scroll_offset ? 0 : sc_attrs.laptop_scroll_offset,

                breakpoint: {},
            },
            $table = get_device_table($iwptp);

        options.breakpoint[iwptp_params.breakpoints.tablet] = {
            left: !sc_attrs.tablet_freeze_left ? 0 : parseInt(sc_attrs.tablet_freeze_left),
            right: !sc_attrs.tablet_freeze_right ? 0 : parseInt(sc_attrs.tablet_freeze_right),
            heading: !!sc_attrs.tablet_freeze_heading && sc_attrs.tablet_freeze_heading !== 'false',

            wrapperWidth: !sc_attrs.tablet_freeze_wrapper_width ? 0 : parseInt(sc_attrs.tablet_freeze_wrapper_width),
            wrapperHeight: !sc_attrs.tablet_freeze_wrapper_height ? 0 : parseInt(sc_attrs.tablet_freeze_wrapper_height),

            tableWidth: !sc_attrs.tablet_freeze_table_width ? 0 : parseInt(sc_attrs.tablet_freeze_table_width),

            offset: !sc_attrs.tablet_scroll_offset ? 0 : parseInt(sc_attrs.tablet_scroll_offset)
        };

        options.breakpoint[iwptp_params.breakpoints.phone] = {
            left: !sc_attrs.phone_freeze_left ? 0 : parseInt(sc_attrs.phone_freeze_left),
            right: !sc_attrs.phone_freeze_right ? 0 : parseInt(sc_attrs.phone_freeze_right),
            heading: !!sc_attrs.phone_freeze_heading && sc_attrs.phone_freeze_heading !== 'false',

            wrapperWidth: !sc_attrs.phone_freeze_wrapper_width ? 0 : parseInt(sc_attrs.phone_freeze_wrapper_width),
            wrapperHeight: !sc_attrs.phone_freeze_wrapper_height ? 0 : parseInt(sc_attrs.phone_freeze_wrapper_height),

            tableWidth: !sc_attrs.phone_freeze_table_width ? 0 : parseInt(sc_attrs.phone_freeze_table_width),

            offset: !sc_attrs.phone_scroll_offset ? 0 : parseInt(sc_attrs.phone_scroll_offset),
        };

        // freeze tables
        if (
            $table.length &&
            // ! $table.data('freezeTable') &&
            typeof jQuery.fn.freezeTable == 'function'
        ) {
            $table.freezeTable(options);
        }

        // convert sidebar to header
        var device = get_device(),
            ft_reload_required = false;

        if (
            ft_reload_required &&
            $table.data('freezeTable')
        ) {
            $table.data('freezeTable').cell_resize();
        }

        // checkboxes
        var $table = iwptp_get_container_original_table($iwptp);

        if (
            $table.data('iwptp_checked_rows') &&
            $table.data('iwptp_checked_rows').length
        ) {
            var $rows = $('.iwptp-row', $table);

            $rows.each(function() {
                var $this = $(this),
                    state = !!$this.data('iwptp_checked');

                $this.trigger('_iwptp_checkbox_change', state);
            })
        }

    })

    // resize
    var resize_timer,
        throttle = 250,
        window_width;

    // throttled window resize event listener
    $(window).on('resize', window_resize);

    function window_resize(e) {
        clearTimeout(resize_timer);
        var new_window_width = window.innerWidth;
        if (new_window_width != window_width) {
            window_width = new_window_width;
            resize_timer = setTimeout(function() {
                trigger_layout('resize');
                recent_orientationchange = false;
            }, throttle);
        }
    }

    // orientation change event listener
    var recent_orientationchange = false;
    $(window).on('orientationchange', function(e) {
        recent_orientationchange = true;
        // trigger_layout('orientationchange');
    });

    function trigger_layout(source) {
        $('.iwptp').trigger('iwptp_layout', { source: source });
    }

    // every load - ajax or page load, needs this
    function after_every_load($container) {

        if ($container.find('.iwptp').length) { // inner tables, depricated
            $container.find('.iwptp').each(function() {
                var $this = $(this);
                after_every_load($this);
            })
        }

        // column heading sort controller
        $container.on('click.iwptp', '.iwptp-heading.iwptp-sortable', window.iwptp_column_heading_sort_handler);

        // sc attrs
        var sc_attrs = {},
            sc_attrs_attr = $container.attr('data-iwptp-sc-attrs');
        if (
            sc_attrs_attr &&
            -1 == $.inArray(sc_attrs_attr, ['[]', '{}'])
        ) {
            sc_attrs = JSON.parse(sc_attrs_attr);
        }
        $container.data('iwptp_sc_attrs', sc_attrs);

        // cart: action="*same page*"
        $('.cart', $container).each(function() {
            $(this).attr('action', window.location.href);
        })

        // cart: wc measurement price calculator init
        if (typeof iwptp_wc_mc_init_cart !== 'undefined') {
            $('.cart', $container).each(iwptp_wc_mc_init_cart);

            $('.wc-measurement-price-calculator-input-help', $container).tipTip({
                attribute: 'title',
                defaultPosition: 'left'
            });
        }

        // ultimate social media icons 
        if (typeof iwptp_sfsi_init !== 'undefined') {
            iwptp_sfsi_init();
        }

        // trigger variation
        prep_variation_options($container);

        // cb select all: duplicate to footer
        duplicate_select_all($container);

        // dynamic filters lazy load
        dynamic_filters_lazy_load($container);

        // checkbox

        // -- add heading cb
        var $tables = iwptp_get_container_tables($container);

        $tables.each(function() {
            var $table = $(this),
                $heading_row = iwptp_get_table_element('.iwptp-heading-row', $table),
                $cb = iwptp_get_table_element('.iwptp-cart-checkbox[data-iwptp-heading-enabled]', $table),
                col_index = [];

            $cb.each(function() {
                var $this = $(this),
                    _col_index = $this.closest('.iwptp-cell').index();

                if (-1 == col_index.indexOf(_col_index)) {
                    col_index.push(_col_index);
                }
            })

            $.each(col_index, function(key, index) {
                var $heading = $('th', $heading_row).eq(index);
                $heading_row.removeClass('iwptp-hide'); // in case it was disabled: had no elements
                if (!$('.iwptp-cart-checkbox-heading', $heading).length) {
                    $heading.prepend('<input type="checkbox" class="iwptp-cart-checkbox-heading">');
                }
            })

        })

        // -- cb trigger
        iwptp_checkbox_trigger_init();

        // -- hide 'add selected to cart' if not required on tablet / phone
        var $device_table = get_device_table($container),
            $add_selected = $('.iwptp-add-selected', $container);

        // if( 
        //   ! $device_table.closest('.iwptp-device-laptop').length && // responsive mode
        //   (
        //     ! $('.iwptp-cart-checkbox', $device_table).length || // no checkbox
        //     iwptp_params.responsive_checkbox_trigger // or responsive checkbox trigger enabled        
        //   )
        // ){
        //   $add_selected.addClass('iwptp-add-selected--responsive-hide');
        // }else{
        //   $add_selected.removeClass('iwptp-add-selected--responsive-hide');
        // }

        // background color
        if (sc_attrs.checked_row_background_color) {
            $('style', $container).first().append('#' + $container.attr('id') + ' .iwptp-row--checked, #' + $container.attr('id') + ' .iwptp-row--checked + .iwptp-child-row   {background: ' + sc_attrs.checked_row_background_color + '! important;}')

        }

        // multirange
        $('.iwptp-range-slider', $container).each(function() {
            iwptp__multirange(this);
        });

        // reset permission
        var query_string = $container.attr('data-iwptp-query-string') ? $container.attr('data-iwptp-query-string') : '',
            parsed = parse_query_string(query_string.substring(1)),
            table_id = $container.attr('data-iwptp-table-id'),
            permit_reset = false,
            $reset = $('.iwptp-reset', $container);

        if ($reset.length) {
            $.each(parsed, function(key, val) {
                if (-1 == $.inArray(key, [table_id + '_device', table_id + '_filtered'])) {
                    permit_reset = true;
                }
            })

            if (permit_reset) {
                $reset.removeClass('iwptp-disabled')
            } else {
                $reset.addClass('iwptp-disabled')
            }
        }

        // wpc smart compare
        if (
            typeof wooscpGetCookie == 'function' &&
            typeof wooscpVars == 'object'
        ) {
            var compare_items__string = wooscpGetCookie('wooscp_products_' + wooscpVars.user_id);
            if (compare_items__string) {
                var compare_items = compare_items__string.split(",");
                compare_items.forEach(function(item) {
                    $('.wooscp-btn-' + item, $container).each(function() {
                        var $this = $(this);
                        $this.addClass('wooscp-btn-added');
                        $this.text(wooscpVars.button_text_added);
                    })
                })

            }
        }

        // nav filter feedback
        nav_filter_feedback($container.find('.iwptp-navigation'));

        // sonaar integration
        sonaar_player_auto_status();

        // hide empty columns
        hide_empty_columns($container);

        // publish
        $container.trigger('iwptp_after_every_load');

        // trigger cart and its view
        if (window.iwptp_cart_result_cache) {
            iwptp_cart({
                payload: {
                    use_cache: true
                }
            });
        }

        // layout
        $container.trigger('iwptp_layout', { source: 'after_every_load' });

    }

    function iwptp_get_container_tables($container) {
        return iwptp_get_shell_element('.iwptp-table:not(.frzTbl-clone-table)', '.iwptp', $container);
    }

    function iwptp_get_container_element(element_selector, $container) {
        return iwptp_get_shell_element(element_selector, '.iwptp', $container);
    }

    function iwptp_get_table_element(element_selector, $table) {
        return iwptp_get_shell_element(element_selector, '.iwptp-table:not(.frzTbl-clone-table)', $table);
    }

    function iwptp_get_shell_element(element_selector, shell_selector, $shell) {
        return $(element_selector, $shell).filter(function() {
            var $this = $(this);
            return $this.closest(shell_selector).is($shell);
        })
    }

    // hide empty columns
    function hide_empty_columns($container) {
        var _sc_attrs = $container.attr('data-iwptp-sc-attrs'),
            sc_attrs = _sc_attrs && _sc_attrs !== '{}' ? JSON.parse(_sc_attrs) : {};

        if (!sc_attrs.hide_empty_columns) {
            return;
        }

        var $table = iwptp_get_container_original_table($container);

        $('.iwptp-cell', $table).removeClass('iwptp-hide');

        $table.each(function() {
            var column_count = $table.find('.iwptp-row').eq(0).children().length;

            while (column_count) {
                var column_cells = $table.find('.iwptp-cell:nth-child(' + column_count + ')'),
                    empty_column_cells = $table.find('.iwptp-cell:nth-child(' + column_count + ')').filter(':empty');

                if (column_cells.length == empty_column_cells.length) {
                    column_cells
                        .add($table.find('.iwptp-heading:nth-child(' + column_count + ')'))
                        .addClass('iwptp-hide');
                }

                --column_count;
            }
        })
    }

    // lazy load
    function lazy_load_start() {
        if (!window.iwptp_lazy_loaded) {
            $('.iwptp-lazy-load').each(function() {
                var $this = $(this);
                attempt_ajax($(this), false, false, 'lazy_load');
            })
            window.iwptp_lazy_loaded = true;
        }
    }

    lazy_load_start();

    // get rows including freeze
    function get_product_rows($elm) {
        var $row = $elm.closest('.iwptp-row'),
            product_id = $row.attr('data-iwptp-product-id'),
            variation_id = $row.attr('data-iwptp-variation-id'),
            $scroll_wrapper = $elm.closest('.iwptp-table-scroll-wrapper'),
            row_selector;

        if (variation_id) {
            row_selector = '[data-iwptp-variation-id="' + variation_id + '"].iwptp-row.iwptp-product-type-variation';
        } else {
            row_selector = '[data-iwptp-product-id="' + product_id + '"].iwptp-row:not(.iwptp-product-type-variation)';
        }

        return $(row_selector, $scroll_wrapper);
    }

    // button click listener
    $('body').on('click', '.iwptp-button', button_click);

    function button_click(e) {
        var $button = $(this),
            link_code = $button.attr('data-iwptp-link-code'),
            $product_rows = get_product_rows($button),
            product_id = $product_rows.attr('data-iwptp-product-id'),
            is_variable = $product_rows.hasClass('iwptp-product-type-variable'),
            complete_match = $product_rows.data('iwptp_complete_match'),
            is_variation = $product_rows.hasClass('iwptp-product-type-variation'),
            is_composite = $product_rows.hasClass('iwptp-product-type-composite'),
            is_bundle = $product_rows.hasClass('iwptp-product-type-woosb'),
            has_addons = $product_rows.hasClass('iwptp-product-has-addons'),
            has_measurement = $product_rows.hasClass('iwptp-product-has-measurement'),
            has_nyp = $product_rows.hasClass('iwptp-product-has-name-your-price'),
            qty = '',
            params = {
                payload: {
                    mini_cart: '',
                    products: {},
                    variations: {},
                    attributes: {},
                    addons: {},
                    measurement: {},
                    nyp: {}, // name your price
                }
            };

        if ($button.closest('.iwptp').find('.iwptp-mini-cart').length > 0) {
            params.payload.mini_cart = $button.closest('.iwptp').find('.iwptp-mini-cart').attr('data-settings');
        }

        if ($button.closest('tr').find('.iwptp-short-message').length > 0) {
            params.payload['short_message'] = $button.closest('tr').find('.iwptp-short-message').val();
        }

        if ($('body').hasClass('iwptp-photoswipe-visible')) {
            e.preventDefault();
            return;
        }

        if (-1 !== $.inArray(link_code, ['product_link', 'external_link', 'custom_field', 'custom_field_media_id', 'custom_field_acf', 'custom'])) {
            return;
        }

        e.preventDefault();

        // validation

        // -- variable product
        if (is_variable) {
            var variation_found = $product_rows.data('iwptp_variation_found'),
                variation_selected = $product_rows.data('iwptp_variation_selected'),
                variation_available = $product_rows.data('iwptp_variation_available');
            variation_ops = $product_rows.data('iwptp_variation_ops');

            // if row has variation selection options but customer did not make selections, show error
            if (variation_ops) {
                if (!variation_selected) {
                    alert(iwptp_i18n.i18n_make_a_selection_text);
                    return;
                }

                if (!variation_found) {
                    alert(iwptp_i18n.i18n_no_matching_variations_text);
                    return;
                }

                if (!variation_available) {
                    alert(iwptp_i18n.i18n_unavailable_text);
                    return;
                }
            }
        }

        // -- disabled
        if (!is_variable && $button.hasClass('iwptp-disabled')) {
            return;
        }

        // -- name your price
        if (has_nyp) {
            var $nyp = get_nyp_input_element($product_rows);
            if ($nyp.length) {
                var error = false,
                    name = $nyp.attr('data-iwptp-product-name'),
                    min = $nyp.attr('min'),
                    max = $nyp.attr('max');

                if (!$nyp.val()) {
                    error = iwptp_nyp_error_message_templates['empty'];

                } else if (
                    min &&
                    $nyp.val() < parseFloat(min)
                ) {
                    error = iwptp_nyp_error_message_templates['minimum_js'].replace(
                        '%%MINIMUM%%',
                        woocommerce_nyp_format_price(min, woocommerce_nyp_params.currency_format_symbol, true)
                    );

                } else if (
                    max &&
                    $nyp.val() > parseFloat(max)
                ) {
                    error = iwptp_nyp_error_message_templates['maximum_js'].replace(
                        '%%MAXIMUM%%',
                        woocommerce_nyp_format_price(max, woocommerce_nyp_params.currency_format_symbol, true)
                    );
                }

                if (error) {
                    alert(error);
                    return;
                }
            }
        }

        // prepare params

        // -- quantity
        var $iwptp_qty = $('.iwptp-quantity input.qty, .iwptp-quantity > select.iwptp-qty-select', $product_rows),
            $wc_qty = $('.cart .qty', $product_rows);

        if ($wc_qty.length) { // from WooCommerce form's qty field
            qty = $wc_qty.val();
        }

        if ($iwptp_qty.length) { // from IWPTPL's own qty element
            var val = parseFloat($iwptp_qty.val());
            if (isNaN(val) || !parseFloat($iwptp_qty.val())) {
                $iwptp_qty.filter('input').first().each(function() {
                    var $this = $(this),
                        min = $this.attr('data-iwptp-min');

                    $this.val(min);
                    limit_qty_controller($this.parent('iwptp-quantity'));
                    val = $this.val();
                })
            }

            qty = val;
        }

        params.payload.products[product_id] = qty;

        // -- addons
        if (has_addons) {
            var addons = iwptp_get_addons($product_rows);
            if (!$.isEmptyObject(addons)) {
                params.payload.addons[product_id] = addons;
            }
        }

        // -- measurement
        if (has_measurement) {
            var measurement = get_measurement($product_rows);
            if (!$.isEmptyObject(measurement)) {
                params.payload.measurement[product_id] = measurement;
            }
        }

        // -- name your price
        if (has_nyp) {
            var nyp = get_nyp($product_rows);
            if (nyp) {
                params.payload.nyp[product_id] = nyp;
            }
        }

        // -- variation
        if (is_variation) {

            var variation_id = $product_rows.attr('data-iwptp-variation-id'),
                variation_attributes = JSON.parse($product_rows.attr('data-iwptp-variation-attributes')),
                $missing_attribute_select = $('.iwptp-select-variation-attribute-term', $product_rows);

            if ($missing_attribute_select.length) {
                $missing_attribute_select.each(function() {
                    var $this = $(this),
                        attribute = $this.attr('data-iwptp-attribute'),
                        term = $this.val();

                    if (term) {
                        variation_attributes[attribute] = term;
                    }
                })
            }

            if (typeof params.payload.variations[product_id] === 'undefined') {
                params.payload.variations[product_id] = {};
            }
            params.payload.variations[product_id][variation_id] = qty;
            params.payload.attributes[variation_id] = variation_attributes;

        } else if ($product_rows.hasClass('iwptp-product-type-variable')) {

            var variation_id = $product_rows.data('iwptp_variation_id'),
                variation_attributes = $product_rows.data('iwptp_attributes');

            if (variation_id) {
                if (typeof params.payload.variations[product_id] === 'undefined') {
                    params.payload.variations[product_id] = {};
                }
                params.payload.variations[product_id][variation_id] = qty;
            }

            if (variation_attributes) {
                params.payload.attributes[variation_id] = variation_attributes;
            }
        }

        // prepare 'ajax_data' (required for non-AJAX req, submit over POST)
        var ajax_data = {
            'action': 'iwptp_add_to_cart',
            'nonce': iwptp_params.ajax_nonce,
            'add-to-cart': $product_rows.attr('data-iwptp-product-id'),
            'product_id': product_id,
            'quantity': qty,
        };

        // -- addons
        if (has_addons) {
            if (!$.isEmptyObject(addons)) {
                $.extend(ajax_data, addons);
            }
        }

        // -- measurement (submit via post)
        if (has_measurement) {
            var measurement = get_measurement($product_rows);
            if (!$.isEmptyObject(measurement)) {
                $.extend(ajax_data, measurement);
            }
        }

        // -- name your price (submit via post)
        if (has_nyp) {
            var nyp = get_nyp($product_rows);
            if (nyp) {
                ajax_data.nyp = nyp;
            }
        }

        // -- variation (submit via post)
        if (is_variable || is_variation) {
            if (variation_id) {
                ajax_data.variation_id = variation_id;
            }

            if (variation_attributes) {
                $.extend(ajax_data, variation_attributes);
            }
        }

        // receive notices from server?
        ajax_data.return_notice = (link_code == "cart_ajax");

        // modal required
        if (
            is_composite ||
            is_bundle ||
            (
                is_variable &&
                !complete_match
            ) ||
            (
                is_variation &&
                is_incomplete_variation(variation_attributes)
            ) ||
            (
                has_addons &&
                !params.payload.addons[product_id]
            ) ||
            (
                has_measurement &&
                !params.payload.measurement[product_id]
            ) ||
            (
                has_nyp &&
                !params.payload.nyp[product_id]
            )
        ) {

            // deploy modal immediately if it's in cache        
            if (typeof window.iwptp_product_form[product_id] !== 'undefined') {
                deploy_product_form_modal(window.iwptp_product_form[product_id], $button, ajax_data);

                // else fetch modal from server and deploy
            } else {
                ajax_data.action = 'iwptp_get_product_form_modal';
                ajax_data.nonce = iwptp_params.ajax_nonce,
                ajax_data.lang = iwptp_i18n.lang;
                delete ajax_data['add-to-cart'];

                $.ajax({
                        url: iwptp_params.wc_ajax_url.replace("%%endpoint%%", "iwptp_get_product_form_modal"),
                        method: 'POST',
                        beforeSend: function() {
                            window.iwptp_modal__last_requested_product_id = product_id;
                            deploy_loading_modal();
                        },
                        data: ajax_data
                    })
                    .done(function(response) {
                        window.iwptp_product_form[product_id] = response;

                        if (product_id === window.iwptp_modal__last_requested_product_id) { // skip if req. superseded            
                            $('.iwptp-product-form-loading-modal').trigger('iwptp_close'); // close loading modal
                            deploy_product_form_modal(response, $button, ajax_data);
                        }
                    })
            }

            return false;
        }

        // all required info already available, no need for modal
        if (link_code == "cart_ajax") {
            iwptp_cart(params);
        } else {
            submit_via_post($button.attr('href'), ajax_data);
        }
    }

    function deploy_product_form_modal(markup, $button, ajax_data) {
        var $modal = $(markup);
        $modal.appendTo('body');
        $('body').addClass('iwptp-modal-on');
        prep_product_form($modal, $button, ajax_data);
        $('body').trigger('iwptp_product_modal_ready');
    }

    function deploy_loading_modal() {
        var $loading_modal = $($('#tmpl-iwptp-product-form-loading-modal').html());
        $('body').append($loading_modal);
        $loading_modal.on('iwptp_close', function() {
            $loading_modal.remove();
        })
    }

    function is_incomplete_variation(variation_attributes) {
        var is_incomplete_variation = false;
        $.each(variation_attributes, function(key, value) {
            if (!value) {
                is_incomplete_variation = true;
                return false;
            }
        });

        return is_incomplete_variation;
    }

    function submit_via_post(href, data) {
        // redirect by form
        var $form = $('<form method="POST" action="' + href + '" style="display: none;"></form>');
        $.each(data, function(key, val) {
            if (key == 'action') return; // continue
            var $input = $('<input type="hidden" name="' + key + '" value="" />');
            $input.val(val);
            $form.append($input);
        })
        $form.append('<input type="hidden" name="iwptp_request" value="true" />');
        $form.appendTo($('body')).submit();
    }

    function prep_product_form($modal, $button, pre_select) {
        var link_code = $button.attr('data-iwptp-link-code'),
            href = link_code == 'cart_ajax' ? '' : $button.attr('href');

        $modal.on('iwptp_close', function() {
            $modal.remove();
            $('body').removeClass('iwptp-modal-on');
        })

        $('.cart', $modal).each(function() {
            var $form = $(this);

            if ($form.hasClass('variations_form')) {
                $form.wc_variation_form();

            } else { // simple product (probably with addon or measurement)
                $form.append('<input name="add-to-cart" type="hidden" value="' + pre_select['product_id'] + '">');

            }

            // init addons
            if ($.fn.init_addon_totals) {
                $form.init_addon_totals();
            }

            if (typeof wcPaoInitAddonTotals === 'object') {
                wcPaoInitAddonTotals.init($form);
            }

            // init measurement
            if (typeof iwptp_wc_mc_init_cart !== 'undefined') {
                $form.each(iwptp_wc_mc_init_cart);
            }

            // cart: name your price 
            if (typeof jQuery.fn.wc_nyp_form !== 'undefined') {
                $form.wc_nyp_form();
            }

            $form.attr('action', href);

            $('.qty', $form).attr('autocomplete', 'off');

            if (pre_select) {
                $.each(pre_select, function(key, val) {
                    var $control = $form.find('[name=' + key + ']');
                    if ($control.is('input.qty')) {

                        // working on input
                        val = parseFloat(val);
                        var min = $control.attr('min') ? parseFloat($control.attr('min')) : 0;
                        var max = $control.attr('max') ? parseFloat($control.attr('max')) : false;

                        // respect min
                        if (val < min || isNaN(val)) {
                            val = min;
                        }

                        // respect max
                        if (max && val > max) {
                            val = max;
                        }
                    }
                    $control.val(val);
                })
            }

            // try and apply quantity on default variation      
            if (pre_select.quantity) {
                $form.one("show_variation", function() {
                    var $form_qty = $('.qty', $form),
                        min = $form_qty.attr('min'),
                        max = $form_qty.attr('max');
                    if (
                        (!min ||
                            min <= pre_select.quantity
                        ) &&
                        (!max ||
                            max >= pre_select.quantity
                        )
                    ) {
                        $form_qty.val(pre_select.quantity);
                    }
                });
            }

            if (link_code == 'cart_ajax') {
                $form.on('submit', function(e) {
                    e.preventDefault();

                    var external_payload = {}

                    $.each($form.serializeArray(), function(i, field) {
                        if (typeof external_payload[field.name] === 'undefined') {
                            external_payload[field.name] = field.value;

                        } else {
                            // should be array
                            if (typeof external_payload[field.name] !== 'object') {
                                external_payload[field.name] = [external_payload[field.name]];
                            }
                            external_payload[field.name].push(field.value);

                        }
                    });

                    iwptp_cart({
                        external_payload: external_payload,
                        payload: { variation_form: true }
                    });

                    $modal.trigger('iwptp_close');
                })
            }

            // reset qty in row
            var $rows = iwptp_get_sibling_rows($button.closest('.iwptp-row'));
            $rows.find('.qty[data-iwptp-return-to-initial=1]').val(0).first().trigger('change');

        });
    }

    function disable_button($button, add_condition) {
        if (add_condition) {
            $button.addClass(add_condition);
        }

        $button.addClass("iwptp-disabled");
    }

    function enable_button($button, clear_condition) {
        if (clear_condition) {
            $button.removeClass(clear_condition);
        }

        if ( // list of conditions
            !$button.hasClass('iwptp-all-variations-out-of-stock') &&
            !$button.hasClass('iwptp-variation-out-of-stock') &&
            !$button.hasClass('iwptp-no-variation-selected') &&
            !$button.hasClass('iwptp-quantity-input-error') &&
            !$button.hasClass('iwptp-out-of-stock')
        ) {
            $button.removeClass("iwptp-disabled");
        }
    }

    function loading_badge_on_button($button) {
        disable_button($button)
        if (!$button.find('.iwptp-cart-badge-refresh').length) {
            var svg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-loader" color="#384047"><line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="6" y2="12"></line><line x1="18" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line></svg>';
            $button.append('<i class="iwptp-cart-badge-refresh">' + svg + '</i>');
        }
    }

    function add_count_badge_to_button(in_cart, $button) {
        if (!parseFloat(in_cart)) {
            $('.iwptp-cart-badge-number, .iwptp-cart-badge-refresh', $button).remove();
            return;
        }

        // if( $button.closest('.iwptp-row').hasClass('iwptp-sold-individually') ){
        //   in_cart = '✓';
        // }    

        if (!$button.find('.iwptp-cart-badge-number').length) {
            $button.append('<i class="iwptp-cart-badge-number">' + in_cart + '</i>');
        } else {
            $button.find('.iwptp-cart-badge-number').html(in_cart);
        }

        if ($button.find('.iwptp-cart-badge-refresh').length) {
            $button.find('.iwptp-cart-badge-refresh').remove();
        }

    }

    // search
    // -- submit
    $('body').on('click', '.iwptp-search-submit', search_submit);
    $('body').on('keydown', '.iwptp-search-input', search_submit);

    function search_submit(e) {
        var $this = $(this),
            $search = $this.closest('.iwptp-search'),
            $input = $search.find('.iwptp-search-input'),
            table_id = $search.attr('data-iwptp-table-id'),
            $container = $('#iwptp-' + table_id),
            $nav_modal = $this.closest('.iwptp-nav-modal'),
            $nav = $this.closest('.iwptp-navigation'),
            keyword = $input.val().trim();
        query = $input.attr('name') + '=' + keyword,
            $wrapper = $input.closest('.iwptp-search-wrapper'),
            append = !$wrapper.hasClass('iwptp-search--reset-others');

        if (
            ( // submit button is clicked
                $(e.target).closest('.iwptp-search-submit').length &&
                e.type == 'click'
            ) ||
            ( // enter key pressed on input
                $(e.target).is('.iwptp-search-input') &&
                e.type == 'keydown' &&
                (
                    e.keyCode == 13 ||
                    e.which == 13
                )
            )
        ) {

            if ($nav_modal.length) {
                $('.iwptp-nm-apply').click();
                return;
            }

            if (append) {
                $nav.trigger('change');
            } else { // reset other filters
                attempt_ajax($container, query, append, 'filter');
            }

            if ($nav_modal.length) {
                $nav_modal.trigger('iwptp_close');
            }
        }
    }
    // -- clear
    $('body').on('click', '.iwptp-search-clear', function(e) {
        var $this = $(this),
            $search = $this.closest('.iwptp-search'),
            $input = $search.find('.iwptp-search-input'),
            table_id = $search.attr('data-iwptp-table-id'),
            $container = $('#iwptp-' + table_id),
            $nav_modal = $this.closest('.iwptp-nav-modal'),
            query = '&' + $input.attr('name') + '=',
            append = true;
        $input.val('');

        if ($nav_modal.length) {
            $('.iwptp-nm-apply').click();
            return;
        }

        attempt_ajax($container, query, append, 'filter');

        if ($nav_modal.length) {
            $nav_modal.trigger('iwptp_close');
        }
    })

    // download button - responsive fix
    if (iwptp_params.initial_device !== 'laptop') {
        $('body').on('click', '.iwptp-button[download]', function(e) {
            e.preventDefault();
            var $this = $(this),
                url = $this.attr('href');

            if (url) {
                window.open(url, '_blank', false)
            }
        })
    }

    // dropdown / tooltip

    // currently assigned trigger ev
    // -- default
    window.iwptp_global_tooltip_trigger_mode = 'hover'; // hover (default) | click
    // -- switch to click
    $(window).on('touchstart', function() {
            window.iwptp_global_tooltip_trigger_mode = 'click';
        })
        // -- switch to hover
    $(window).on('resize', function() {
        window.iwptp_global_tooltip_trigger_mode = 'hover';
    })

    var target_selector = '.iwptp-dropdown, .iwptp-tooltip',
        $body = $('body');

    $body.on('mouseenter', target_selector, dropdown_mouse_open);
    $body.on('mouseleave', target_selector, dropdown_mouse_close);
    $body.on('click', dropdown_touch_toggle);

    function dropdown_mouse_open(e) {
        var $this = $(this);

        if (
            $this.hasClass('iwptp-tooltip--open-on-click') ||
            iwptp_global_tooltip_trigger_mode == 'click'
        ) {
            return;
        }

        // hover intent
        if ($this.hasClass('iwptp-tooltip--hover-intent-enabled')) {
            var clear_timeout = setTimeout(function() {
                $this.addClass('iwptp-open');
                fix_tooltip_position($this);
            }, 100);

            $this.data('iwptp_hover_intent_clear_timeout', clear_timeout);

            return;
        }

        $this.addClass('iwptp-open');
        fix_tooltip_position($this);
    }

    function dropdown_mouse_close(e) {
        var $this = $(this);

        if (
            $this.hasClass('iwptp-tooltip--open-on-click') ||
            iwptp_global_tooltip_trigger_mode == 'click'
        ) {
            return;
        }

        // hover intent
        if ($this.hasClass('iwptp-tooltip--hover-intent-enabled')) {
            var clear_timeout = $this.data('iwptp_hover_intent_clear_timeout');
            if (clear_timeout) {
                clearTimeout(clear_timeout);
            }
        }

        $this.removeClass('iwptp-open');
    }

    function dropdown_touch_toggle(e) {
        var $target = $(e.target),
            container_selector = '.iwptp-dropdown, .iwptp-tooltip',
            content_selector = '> .iwptp-dropdown-menu, > .iwptp-tooltip-content-wrapper > .iwptp-tooltip-content',
            $dropdown = $target.closest(container_selector),
            $content = $dropdown.find(content_selector),
            $body = $('body');

        if (
            $dropdown.length &&
            iwptp_global_tooltip_trigger_mode == 'hover' &&
            !$dropdown.hasClass('iwptp-tooltip--open-on-click')
        ) {
            return;
        }

        // clicked outside any tootltip / filter
        if (!$dropdown.length) {
            // close all
            $body.find(container_selector).removeClass('iwptp-open');
            return;
        }

        if ($dropdown.length) {
            // clicked in content
            if ($target.closest($content).length) {
                // return;

                // clicked on trigger
            } else {
                // close all others
                var $parents = $dropdown.parents(container_selector);
                $body.find(container_selector).not($dropdown.add($parents)).removeClass('iwptp-open');

                $dropdown.toggleClass('iwptp-open');

                // close all children
                if (!$dropdown.hasClass('iwptp-open')) {
                    $dropdown.find(container_selector).removeClass('iwptp-open');
                }

                // popup enabled
                if ($dropdown.hasClass('iwptp-tooltip--popup-enabled')) {
                    if ($dropdown.hasClass('iwptp-open')) { // need to run open logic earlier
                        $body.addClass('iwptp-tooltip-popup-displayed');
                    } else {
                        $body.removeClass('iwptp-tooltip-popup-displayed');
                    }
                }

            }
        }

        fix_tooltip_position($dropdown);
    }

    function fix_tooltip_position($tooltip) {
        // correct position
        var $content = $tooltip.find(' > .iwptp-dropdown-menu, > .iwptp-tooltip-content-wrapper > .iwptp-tooltip-content'),
            content_width = $content.outerWidth(false),
            offset_left = $content.offset().left,
            page_width = $(window).width();

        if ($tooltip.hasClass('iwptp-tooltip')) { // tooltip

            // narrow width
            $content.css('max-width', '');
            var $container,
                margin;

            if ($tooltip.hasClass('iwptp-tooltip--popup-enabled')) {
                $container = $('body');
                margin = 40;

            } else if ($tooltip.closest('.iwptp-left-sidebar-toggle').length > 0) {
                $container = $tooltip.closest('.iwptp-left-sidebar-toggle');
                margin = 20;
            } else {
                $container = $tooltip.closest('.iwptp-table-scroll-wrapper-outer').length ? $tooltip.closest('.iwptp-table-scroll-wrapper-outer') : $tooltip.closest('.iwptp-navigation');
                margin = 20;
            }

            var container_rect = $.extend({}, $container[0].getBoundingClientRect()),
                content_rect = $content[0].getBoundingClientRect();

            var $freezeTable = $('.frzTbl-table', $container);

            if ($content.closest($freezeTable).length) { // target is inside the original freeze_table
                var $left_freeze_column = $freezeTable.data('freezeTable').el.$frozenColumnsLeft,
                    $right_freeze_column = $freezeTable.data('freezeTable').el.$frozenColumnsRight;

                container_rect.left += $left_freeze_column.width();
                container_rect.right -= $right_freeze_column.width();
                container_rect.width = container_rect.width - $left_freeze_column.width() - $right_freeze_column.width();
            }

            if (container_rect.width < parseInt(content_rect.width) + (margin * 2)) {
                $content.css('max-width', container_rect.width - (margin * 2));
            }

            // vertical position
            var $content_wrapper = $content.parent(),
                content_wrapper_width = $content_wrapper.width();

            $content_wrapper.attr('data-iwptp-position', '');
            content_rect = $content[0].getBoundingClientRect(); // refresh

            if (container_rect.bottom < content_rect.bottom + 30) {
                $content.parent().attr('data-iwptp-position', 'above');
            }

            // horizontal position
            $content.css({ 'left': '', 'right': '' });
            content_rect = $content[0].getBoundingClientRect(); // refresh

            var arrow_margin = 20; // how close arrow gets to left/ right edge

            if (content_rect.left - 15 < container_rect.left) { // excess left
                var left = 15 + container_rect.left - content_rect.left,
                    limit = (content_wrapper_width / 2) - arrow_margin;

                if (left > limit) {
                    left = limit;
                }

                $content.css('left', left);

            } else if (content_rect.right + 15 > container_rect.right) { // excess right        
                var right = content_rect.right - container_rect.right + 15,
                    limit = (content_wrapper_width / 2) - arrow_margin;

                if (right > limit) {
                    right = limit;
                }

                $content.css('right', right);

            }

        } else { // dropdown
            if (content_width + 30 > page_width) {
                $content.outerWidth(page_width - 30);
                var content_width = $content.outerWidth(false);
            }

            if ($content.offset().left + content_width > page_width) { // offscreen right
                var offset_required = $content.offset().left + content_width - page_width;
                $content.css('left', '-=' + (offset_required + 15));
            } else if ($content.offset().left < 0) { // offscreen left
                $content.css('left', (Math.abs($content.offset().left - 15)));
            }

        }

        // tooltip arrow
        if ($tooltip.hasClass('iwptp-tooltip')) {
            var $label = $tooltip.find('> .iwptp-tooltip-label'),
                offset_left = $label.offset().left,
                width = $label.outerWidth(),
                $arrow = $('> .iwptp-tooltip-arrow', $content);
            if ($container.hasClass('iwptp-left-sidebar-toggle')) {
                offset_left += 4;
            }
            $arrow.css('left', (offset_left - $content.offset().left + (width / 2)) + 'px');
        }
    }

    // tooltip with content hover disabled
    $('body').on('click mouseover', '.iwptp-tooltip-content', function() {
        var $this = $(this),
            $tooltip = $this.closest('.iwptp-tooltip');

        if ($tooltip.hasClass('iwptp-tooltip--hover-disabled')) {
            $tooltip.removeClass('iwptp-open');
        }
    })

    // close dropdown when grab and scroll starts
    $('body').on('freeze_table__grab_and_scroll__start', function() {
        $('.iwptp-dropdown.iwptp-open').removeClass('iwptp-open');
    })

    // apply nav filters
    $('body').on('change', '.iwptp-navigation', apply_nav);

    function apply_nav(e) {

        var $target = $(e.target),
            $container = $target.closest('.iwptp'),
            $nav = $container.find('.iwptp-navigation');

        // skip search filter options input 
        if ($target.closest('.iwptp-search-filter-options').length) {
            return;
        }

        // taxonomy hierarchy
        if ($target.closest('.iwptp-hierarchy').length) {
            var checked = $target.prop('checked');

            // effect on child terms
            if ($target.hasClass('iwptp-hr-parent-term')) {
                var ct_selector = 'input[type=checkbox], input[type=radio]',
                    $child_terms = $target.closest('label').siblings('.iwptp-hr-child-terms-wrapper').find(ct_selector);
                $child_terms.prop('checked', false);
            }

            // effect on parent terms
            var $ancestors = $target.parents('.iwptp-hr-child-terms-wrapper');
            if ($ancestors.length) {
                $ancestors.each(function() {
                    var $parent_term = $(this).siblings('label').find('.iwptp-hr-parent-term');
                    $parent_term.prop('checked', false);
                })
            }
        }

        // range filter
        if ($target.closest('.iwptp-range-filter')) {

            // -- input boxes shouldn't propagate
            if (
                $target.hasClass('iwptp-range-input-min') ||
                $target.hasClass('iwptp-range-input-max') ||
                $target.hasClass('iwptp-range-slider')
            ) {
                return;
            }

            var min = $target.attr('data-iwptp-range-min') || '',
                max = $target.attr('data-iwptp-range-max') || '',
                $range_filter = $target.closest('.iwptp-range-filter'),
                $min = $range_filter.find('.iwptp-range-input-min'),
                $max = $range_filter.find('.iwptp-range-input-max'),
                $range_slider = $range_filter.find('.iwptp-range-slider.original');

            $min.val(min);
            $max.val(max);
            if (!min) {
                min = $range_slider.attr('min');
            }
            if (!max) {
                max = $range_slider.attr('max');
            }

            $range_slider.val(min + ',' + max);
        }

        // search
        if ($target.closest('.iwptp-search').length) {
            return;
        }

        // modal
        if ($target.closest('.iwptp-nav-modal').length) {
            return;
        }

        var $this = $(this),
            $nav = $this.add($this.siblings('.iwptp-navigation')), // combine query from all navs
            $container = $nav.closest('.iwptp'),
            table_id = $container.attr('id').substring(6),
            $nav_clone = $nav.clone();

        nav_clone_operations($nav_clone);

        // build query
        var query = $('<form>').append($nav_clone).serialize();

        // include column sort
        if (!$(e.target).closest('[data-iwptp-filter="sort_by"]').length) {
            var $table = iwptp_get_container_original_table($container),
                $sortable_headings = $('.iwptp-heading.iwptp-sortable:visible', $table),
                $current_sort_col = $sortable_headings.filter(function() {
                    return $(this).find('.iwptp-sorting-icons.iwptp-sorting-asc, .iwptp-sorting-icons.iwptp-sorting-desc').length;
                });
            if ($current_sort_col.length) {
                var col_index = $current_sort_col.attr('data-iwptp-column-index'),
                    order = $current_sort_col.find('.iwptp-sorting-icons.iwptp-sorting-asc').length ? 'ASC' : 'DESC';
                query += '&' + table_id + '_orderby=column_' + col_index + '&' + table_id + '_order=' + order;

            }
        }

        // do not proceed if 'Apply' button is available, just give feedback
        if (
            $nav.find('.iwptp-apply').length &&
            !$(e.target).hasClass('iwptp-navigation')
        ) {
            nav_filter_feedback($nav);
            return;
        }

        attempt_ajax($container, query, false, 'filter');
    }

    function nav_clone_operations($nav_clone) {
        var $reverse_check = $();

        $('[data-iwptp-reverse-value]:not(:checked)', $nav_clone).each(function() {
            var $this = $(this);
            $this.attr('value', $this.attr('data-iwptp-reverse-value'));
            $this.prop('checked', 'checked');
            $reverse_check = $reverse_check.add($this.clone());
        })
        $nav_clone = $nav_clone.add($reverse_check);

        // clean radio names
        $('input[type="radio"]', $nav_clone).each(function() {
            var $this = $(this),
                name = $this.attr('name');

            if (-1 !== name.indexOf('--')) {
                var is_array = name.indexOf('[]');
                name = name.substr(0, name.indexOf('--')) + (is_array ? '[]' : '');
                $this.attr('name', name);
            }
        })
    }

    function nav_filter_feedback($nav) {
        // header nav
        $('.iwptp-filter', $nav.filter('.iwptp-header')).each(function() {
            var $this = $(this),
                filter = $this.attr('data-iwptp-filter'),
                $filter = $this.closest('.iwptp-filter'),
                format = $this.attr('data-iwptp-heading_format__op_selected'),
                radio = $this.find('input[type=radio]').length || $this.hasClass('iwptp-range-filter'),
                checkbox = $this.find('input[type=checkbox]').length,
                $selected = $this.find('input[type=radio]:checked'),
                $checked = $this.find('input[type=checkbox]:checked'),
                checked_count = $checked.length,
                $active_count = $this.find('.iwptp-active-count'),
                radio_permit = false,
                label_append = '',
                $multi_range = $('.iwptp-range-options-main', $filter),
                $multi_range__min = $('.iwptp-range-options-main .iwptp-range-input-min', $filter),
                $multi_range__max = $('.iwptp-range-options-main .iwptp-range-input-max', $filter);

            if ($this.hasClass('iwptp-options-row')) {
                return;
            }

            if (-1 == $.inArray(filter, [
                    'custom_field',
                    'attribute',
                    'category',
                    'taxonomy',
                    'price_range',
                    'rating',
                    'sort_by',
                    'results_per_page',
                    'on_sale',
                    'availability'
                ])) {
                return;
            }

            // mark active filters
            if (
                checked_count ||
                (
                    $selected.val() &&
                    !$selected.closest('.iwptp-default-option').length // sort by
                ) ||
                (
                    $multi_range.length &&
                    (
                        $multi_range__min.val() != $multi_range__min.attr('min') ||
                        $multi_range__max.val() != $multi_range__max.attr('max')
                    )
                )
            ) {
                $this.closest('.iwptp-filter').addClass('iwptp-filter--active');
            } else {
                $this.closest('.iwptp-filter').removeClass('iwptp-filter--active');
            }

            // modify dropdown heading            

            // -- radio append option label
            if (
                radio &&
                format !== 'only_heading'
            ) {
                $this.find('.iwptp-radio-op-selected__heading-append').remove();

                if (!$selected.length ||
                    !$selected.attr('value') // 'show all' op selected
                ) {
                    $this.removeClass('iwptp-radio-op-selected');

                } else { // selected and has value
                    $this.addClass('iwptp-radio-op-selected');
                    label_append = $selected.next()[0].outerHTML;
                    radio_permit = true;

                }

                if (!$selected.length &&
                    filter == 'price_range'
                ) {
                    var min = iwptp_params.currency_symbol + $('.iwptp-range-input-min', $this).val() || 0,
                        max = iwptp_params.currency_symbol + $('.iwptp-range-input-max', $this).val() || 0;

                    label_append = '<span>' + min + ' - ' + max + '<span>';

                    if (
                        $('.iwptp-range-input-min', $this).val() != $('.iwptp-range-input-min', $this).attr('min') ||
                        $('.iwptp-range-input-max', $this).val() != $('.iwptp-range-input-max', $this).attr('max')
                    ) {
                        $this.addClass('iwptp-radio-op-selected');
                        radio_permit = true;
                    }
                }

                if (!$selected.length &&
                    filter == 'custom_field'
                ) {
                    var min = $('.iwptp-range-input-min', $this).val() || 0,
                        max = $('.iwptp-range-input-max', $this).val() || 0;

                    label_append = '<span>' + min + ' - ' + max + '<span>';

                    if (
                        min != $('.iwptp-range-input-min', $this).attr('min') ||
                        max != $('.iwptp-range-input-max', $this).attr('max')
                    ) {
                        $this.addClass('iwptp-radio-op-selected');
                        radio_permit = true;
                    }
                }

                if (radio_permit) {
                    $this.find('.iwptp-dropdown-label').append('<div class="iwptp-radio-op-selected__heading-append">' + label_append + '</div>');
                }

                // -- checkbox append selected option count
            } else if (checkbox) {
                $active_count.remove();

                if (checked_count) {
                    $active_count = $('<span class="iwptp-active-count" style="margin-left: 6px">' + checked_count + '</span>');
                    $('.iwptp-filter-heading .iwptp-dropdown-label', $this).after($active_count);
                }

            }

        })

    }

    // submit range by enter
    $('body').on('keyup', '.iwptp-range-input-min, .iwptp-range-input-max', function(e) {
        var $this = $(this),
            $filters = $this.closest('.iwptp-navigation'),
            code = (e.keyCode ? e.keyCode : e.which);

        if (code == 13) {
            $filters.trigger('change');
        }
    })

    // submit range
    $('body').on('click', '.iwptp-range-submit-button', function(e) {
        var $this = $(this),
            $filters = $this.closest('.iwptp-navigation');

        $filters.trigger('change');
    })

    // clear filter
    $('body').on('click', '.iwptp-clear-filter', function(e) {

        var $clear_filter = $(this),
            $target = $(e.target);

        if ($target.closest('.iwptp-dropdown-menu')) {
            var $sub_option = $target.closest('.iwptp-dropdown-option');
        } else {
            $sub_option = false;
        }

        var $container = $clear_filter.closest('.iwptp'),
            filter = $clear_filter.attr('data-iwptp-filter'),
            $navs = $('> .iwptp-navigation', $container),
            $inputs = $();

        if (filter == 'search') {
            var name = $clear_filter.attr('data-iwptp-search-name'),
                $inputs = $('.iwptp-search-input[name="' + name + '"]', $navs);

        } else if (filter == 'attribute' || filter == 'category' || filter == 'taxonomy') {

            var taxonomy = $clear_filter.attr('data-iwptp-taxonomy'),
                term = $clear_filter.attr('data-iwptp-value'),
                $inputs = $navs.find('.iwptp-filter[data-iwptp-filter="' + filter + '"][data-iwptp-taxonomy="' + taxonomy + '"]').find('input[value="' + term + '"]');

        } else if (filter == 'custom_field') {

            var meta_key = $clear_filter.attr('data-iwptp-meta-key'),
                value = $clear_filter.attr('data-iwptp-value'),
                $filter = $navs.find('.iwptp-filter[data-iwptp-filter="' + filter + '"][data-iwptp-meta-key="' + meta_key + '"]');

            if ($filter.hasClass('iwptp-range-filter')) {
                $inputs = $filter.find('input');
            } else {
                $inputs = $navs.find('.iwptp-filter[data-iwptp-filter="' + filter + '"][data-iwptp-meta-key="' + meta_key + '"]').find('input[value="' + value + '"]');
            }

        } else if (filter == 'price_range') {
            var $inputs = $navs.find('.iwptp-filter[data-iwptp-filter="' + filter + '"]').find('input');

        } else if (filter == 'search') {
            $inputs = $navs.find('input[type=search][data-iwptp-value="' + htmlentity($clear_filter.attr("data-iwptp-value")) + '"]');

        } else if (filter == 'rating') {
            $inputs = $navs.find('.iwptp-filter[data-iwptp-filter="rating"]').find('input');

        }

        $inputs.filter(':input[type=checkbox], :input[type=radio]').prop('checked', false).closest('label.iwptp-active').removeClass('iwptp-active');

        $inputs.filter(':input[type=text], :input[type=number], :input[type=search]').val(''); // search and range input

        $navs.first().trigger('change');

        // remove clear filter
        if (!$clear_filter.siblings('.iwptp-clear-filter').length) {
            $clear_filter.closest('.iwptp-clear-filters-wrapper').remove();
        } else {
            $clear_filter.remove();
        }

    })

    // clear all filters
    $('body').on('click', '.iwptp-clear-filters, .iwptp-clear-all-filters, .iwptp-reset', function(e) {
        e.preventDefault();
        var $this = $(this),
            $container = $this.closest('.iwptp'),
            query = '';

        if (!$this.hasClass('iwptp-disabled')) {
            attempt_ajax($container, query, false, 'filter');
        }
    })

    // sort by column heading
    window.iwptp_column_heading_sort_handler = function() {
        var $this = $(this),
            $sorting = $this.find('.iwptp-sorting-icons');

        if (!$sorting.length) {
            return;
        }

        // if( $this.hasClass('iwptp-instant-sort') ){
        //   return;
        // }

        var order = $sorting.hasClass('iwptp-sorting-asc') ? 'desc' : 'asc',
            col_index = $this.attr('data-iwptp-column-index'),
            $container = $this.closest('.iwptp'),
            table_id = $container.attr('id').substring(6),
            device = 'laptop';

        if ($('.iwptp-sorting-' + order + '-icon', $sorting).hasClass('iwptp-hide')) {
            if ($('.iwptp-sorting-' + order + '-icon', $sorting).siblings().hasClass('iwptp-active')) {
                return;
            } else {
                order = order == 'asc' ? 'desc' : 'asc';
            }
        }

        var query = table_id + '_paged=1&' + table_id + '_orderby=column_' + col_index + '&' + table_id + '_order=' + order + '&' + table_id + '_device=' + device;

        attempt_ajax($container, query, true, false);
    }

    // pagination
    $('body').on('click', '.iwptp-pagination .iwptp-page-numbers:not(.dots):not(.current)', function(e) {
        e.preventDefault();
        var $this = $(this),
            $container = $this.closest('.iwptp'),
            table_id = $container.attr('id').slice(6),
            url = $this.attr('href'),
            index = url.indexOf("?"),
            params = index == -1 ? false : parse_query_string(url.slice(index + 1)),
            page = params ? params[table_id + '_paged'] : 1,
            query = table_id + '_paged=' + page;
        append = true;

        attempt_ajax($container, query, append, 'paginate');
    });

    if ($('.iwptp-table-container .iwptp').css('direction') == 'rtl') {
        $('.iwptp-table-container').addClass('iwptp-rtl')
    } else {
        $('.iwptp-table-container').removeClass('iwptp-rtl')
    }

    // ajax
    function attempt_ajax($container, new_query, append, purpose) {

        if (typeof purpose == 'undefined') {
            throw 'IWPTPL: Define AJAX purpose';
        }

        // combine earlier query
        var query = '',
            earlier_query = $container.attr('data-iwptp-query-string');

        if (append && earlier_query) {
            earlier_query = earlier_query.substring(1);
            query = '?';

            $.each($.extend({},
                parse_query_string(earlier_query),
                parse_query_string(new_query)
            ), function(key, val) {
                if (val !== 'undefined') {
                    query += key + "=" + encodeURIComponent(val) + "&";
                }
            })
            query = query.substring(0, query.length - 1);

        } else {
            query = '?' + new_query;
        }

        // lazy load
        if (purpose == 'lazy_load') {
            query += '&' + window.location.search.substring(1);
        }

        // persist params
        var parsed_params = parse_query_string(window.location.search.substring(1));
        var query_obj = parse_query_string(query.substring(1));

        if (typeof window.iwptp_persist_params !== 'undefined') {
            $.each(iwptp_persist_params, function(index, i) {
                if (
                    parsed_params[i] !== 'undefined' &&
                    typeof parsed_params[i] !== 'undefined' &&
                    typeof query_obj[i] == 'undefined'
                ) {
                    query += '&' + i + '=' + parsed_params[i];
                }
            })
        }

        // device
        var device = 'laptop',
            $scroll_outer = $container.find('.iwptp-table-scroll-wrapper-outer:visible'),
            table_id = $container.attr('data-iwptp-table-id');

        if ($scroll_outer.length) {
            if ($scroll_outer.hasClass('iwptp-device-phone')) {
                device = 'phone';
            } else if ($scroll_outer.hasClass('iwptp-device-tablet')) {
                device = 'tablet';
            }
        } else if ($('body').hasClass('iwptp-nav-modal-on')) {
            $('.iwptp-nav-modal').attr('data-iwptp-device');
        } else if ($('.iwptp-required-but-missing-nav-filter-message, .iwptp-no-results', $container).length) {
            device = $('.iwptp-required-but-missing-nav-filter-message, .iwptp-no-results', $container).attr('data-iwptp-device');
        }

        var query_obj = parse_query_string(query);
        if (query_obj[table_id + '_device'] !== device) {
            query += '&' + table_id + '_device=' + device;
        }

        // shortcode attributes
        // -- parse
        var _sc_attrs = $container.attr('data-iwptp-sc-attrs'),
            sc_attrs = _sc_attrs && _sc_attrs !== '{}' ? JSON.parse(_sc_attrs) : {};

        // disable ajax
        var disable_ajax = !!sc_attrs.disable_ajax;

        // search - orderby relevance
        var new_query_p = new_query ? parse_query_string(new_query) : {},
            earlier_query_p = earlier_query ? parse_query_string(earlier_query.substring(1)) : {},
            search_orderby = sc_attrs.search_orderby ? 'search_orderby' : 'relevance';
        search_order = sc_attrs.search_order ? 'search_order' : '';

        // detect if a new search is taking place in this query
        $.each(new_query_p, function(key, val) {
            if (
                key.indexOf('search') !== -1 &&
                val &&
                (
                    earlier_query_p[key] !== val.replace(/\+/g, ' ')
                )
            ) {
                // reset pagination and search orderby and order
                query += '&' + table_id + '_orderby=' + search_orderby + '&' + table_id + '_order=' + search_order + '&' + table_id + '_paged=1';
                return false;
            }
        });

        // form mode, hide form on submit
        if (
            sc_attrs.form_mode &&
            sc_attrs.hide_form_on_submit
        ) {
            query += '&hide_form=' + table_id;
        }

        // scroll after ajax
        var scroll = true;
        if (-1 !== $.inArray(purpose, ['device_view', 'lazy_load', 'refresh_table'])) {
            scroll = false;
        }

        // table already been filtered?
        if (purpose == 'filter') {
            query += '&' + table_id + '_filtered=true';
        }

        // coming from shop?
        if (parsed_params[table_id + '_from_shop']) {
            query += '&' + table_id + '_from_shop=true';
        }

        // form mode (redirect to shop with params)
        if (
            purpose == 'filter' &&
            sc_attrs.form_mode
        ) {
            // switch table id
            query = query.split(table_id + '_').join(iwptp_params.shop_table_id + '_');

            // don't let lang param repeat, lang=fr,fr error
            var i = iwptp_params.shop_url.indexOf('?lang=');
            if (
                i !== -1 &&
                query.indexOf('lang=') !== -1
            ) {
                iwptp_params.shop_url = iwptp_params.shop_url.substring(0, i);
            }

            if (iwptp_params.shop_url.indexOf('?') == -1) {
                url = iwptp_params.shop_url + (query);
            } else {
                url = iwptp_params.shop_url + '&' + (query.slice(1));
            }

            // disable device requirement
            url += '&' + iwptp_params.shop_table_id + '_device=';

            window.location = url;
            console.log('iwptp notice: redirect to shop');
            return;
        }

        // allow table from cache    
        var permit_cache = !(purpose == 'refresh_table') && typeof WavePlayer === 'undefined';

        // skip ajax, redirect
        if (disable_ajax) {
            window.location = query;
            console.log('iwptp notice: disable ajax');
            return;
        }

        // add shortcode attributes
        // -- add to query
        if (!$.isEmptyObject(sc_attrs)) {
            // applying fix % conflict with % -> %25
            // query += '&' + table_id + '_sc_attrs=' + encodeURIComponent( _sc_attrs.replaceAll('%', '%25') );
            query += '&' + table_id + '_sc_attrs=' + encodeURIComponent(_sc_attrs);
        }

        var url = iwptp_params.wc_ajax_url.replace("%%endpoint%%", "iwptp_ajax") + '&' + query.slice(1),
            data = {
                'id': table_id,
            };

        $.ajax({
            url: url,
            method: 'GET',
            beforeSend: function() {
                $container.addClass('iwptp-loading');

                if (
                    permit_cache &&
                    window.iwptp_cache.exist(query)
                ) {
                    ajax_success(window.iwptp_cache.get(query), $container, scroll, device, purpose);
                    return false;
                }
                return true;
            },
            data: data,
        }).done(function(response) {
            // success
            if (response && response.indexOf('iwptp-table') !== -1) {
                window.iwptp_cache.set(query, response);
                ajax_success(window.iwptp_cache.get(query), $container, scroll, device, purpose);

                // fail
            } else {
                console.log('iwptp notice: query fail');
                window.location = query;

            }

        });

    }

    // helper fn.
    function parse_query_string(query) {
        var vars = query.split("&");
        var query_string = {};
        for (var i = 0; i < vars.length; i++) {
            var pair = vars[i].split("="),
                key = decodeURIComponent(pair[0]),
                value = decodeURIComponent(pair[1]);

            // If first entry with this name
            if (typeof query_string[key] === "undefined") {
                query_string[key] = value;

                // If second entry with this name
            } else if (typeof query_string[key] === "string") {
                var arr = [query_string[key], value];
                query_string[key] = arr;

                // If third or later entry with this name
            } else {
                query_string[key].push(value);

            }
        }
        return query_string;
    }

    // ajax successful
    function ajax_success(response, $container, scroll, device, purpose) {

        $('body').trigger('iwptp_ajax_success', arguments);

        $container = ($container.hasClass('iwptp-table-container')) ? $container : $container.closest('.iwptp-table-container');
        var $new_table = $(response).find('.iwptp');
        $container.html($new_table);

        // audio/video player
        if (typeof window.wp.mediaelement !== 'undefined') {
            window.wp.mediaelement.initialize();
        }

        // inline variation forms need to be force init after ajax
        $('.cart', $new_table).each(function() {
            var $form = $(this);
            if ($form.hasClass('variations_form')) {
                $form.wc_variation_form();
            }
            if ($.fn.init_addon_totals) {
                $form.init_addon_totals();
            }
            if (typeof wcPaoInitAddonTotals === 'object') {
                wcPaoInitAddonTotals.init($form);
            }
        })

        // needs to run on page load as well as ajax load
        after_every_load($new_table);

        var sc_attrs_string = $new_table.attr('data-iwptp-sc-attrs'),
            sc_attrs = (sc_attrs_string && sc_attrs_string !== '{}') ? JSON.parse(sc_attrs_string) : {},
            offset = {
                'laptop': (typeof sc_attrs.laptop_scroll_offset == 'undefined' || sc_attrs.laptop_scroll_offset == '') ? 20 : sc_attrs.laptop_scroll_offset,
                'tablet': (typeof sc_attrs.tablet_scroll_offset == 'undefined' || sc_attrs.tablet_scroll_offset == '') ? 20 : sc_attrs.tablet_scroll_offset,
                'phone': (typeof sc_attrs.phone_scroll_offset == 'undefined' || sc_attrs.phone_scroll_offset == '') ? 20 : sc_attrs.phone_scroll_offset,
            };

        // scroll to top & set history
        var $scroll_target = $new_table;

        if (scroll) {
            if (sc_attrs[device + '_auto_scroll']) {
                var offset = offset[device];
                if (isNaN(offset)) {
                    if (typeof offset === 'string') { // selector
                        offset = $(offset).height();
                    } else if (typeof offset === 'object') { // jQuery object
                        offset = offset.height();
                    }
                }

                $('html, body').animate({
                    scrollTop: $scroll_target.offset().top - parseFloat(offset),
                }, 200);
            }

            var query = $new_table.attr('data-iwptp-query-string'),
                _sc_attrs = $new_table.attr('data-iwptp-sc-attrs'),
                sc_attrs = _sc_attrs && _sc_attrs !== '{}' ? JSON.parse(_sc_attrs) : {};

            if (query && typeof window.history !== 'undefined' && !sc_attrs.disable_url_update) {
                history.replaceState({}, $('title').text(), query);
            }
        }

        if ($('.iwptp-table-container .iwptp').css('direction') == 'rtl') {
            $('.iwptp-table-container').addClass('iwptp-rtl')
        } else {
            $('.iwptp-table-container').removeClass('iwptp-rtl')
        }
    }
    window.iwptp_attempt_ajax = attempt_ajax;

    // variable product modal form
    //-- close modal
    $('body').on('click', '.iwptp-modal, .iwptp-close-modal', function(e) {
        var $target = $(e.target),
            $modal = $(this).closest('.iwptp-modal');
        if (
            $target.hasClass('iwptp-modal') ||
            $target.closest('.iwptp-close-modal').length
        ) {
            $modal.trigger('iwptp_close');
        }
    })

    window.iwptp_update_cart_items = function(cart) {
        var cart_products = {},
            total = 0;
        $.each(cart, function(key, item) {
            if (!cart_products[item.product_id]) {
                cart_products[item.product_id] = 0;
            }

            if (item.variation_id && !cart_products[item.variation_id]) {
                cart_products[item.variation_id] = 0;
            }

            cart_products[item.product_id] += item.quantity;

            if (item.variation_id) {
                cart_products[item.variation_id] += item.quantity;
            }

            total += item.quantity;
        })

        // -- update each product row
        $('.iwptp-row').each(function() {
            var $this = $(this),
                id = $this.attr('data-iwptp-variation-id') ? $this.attr('data-iwptp-variation-id') : $this.attr('data-iwptp-product-id'),
                qty = cart_products[id] ? cart_products[id] : 0,
                $badge = $this.find('.iwptp-cart-badge-number'),
                $remove = $this.find('.iwptp-remove');

            $this.attr('data-iwptp-in-cart', qty);
            if (qty) {
                add_count_badge_to_button(qty, $badge.closest('.iwptp-button'));

            } else {
                $badge.text('');
            }
        })

        iwptpCheckMiniCartVisibility();
    }

    // anchor tag
    $('body').on('click touchstart', '[data-iwptp-href]', function() {
        window.location = $(this).attr('data-iwptp-href');
    })

    // accordion
    //-- filters
    $('body').on('click', '.iwptp-left-sidebar .iwptp-filter > .iwptp-filter-heading', function(e) {
            if ($(e.target).closest('.iwptp-tooltip').length) {
                return;
            }

            var $this = $(this),
                $filter = $this.closest('.iwptp-filter');
            $filter.toggleClass('iwptp-filter-open');
        })
        //-- filter heading clicked directly
    $('body').on('click', '.iwptp-left-sidebar .iwptp-filter:not(.iwptp-filter-open)', function(e) {
            var $this = $(this);
            if (e.target === this) {
                $this.addClass('iwptp-filter-open');
            }
        })
        //-- taxonomy parent
    $('body').on('click', '.iwptp-ac-icon', function(e) {
        var $this = $(this);
        $this.closest('.iwptp-accordion').toggleClass('iwptp-ac-open');
        e.stopPropagation();
        return false;
    })

    // nav modal
    function nav_modal(e) {
        var $button = $(e.target).closest('.iwptp-rn-button'),
            modal_type = $button.attr('data-iwptp-modal'),
            $iwptp = $button.closest('.iwptp'),
            iwptp_id = $iwptp.attr('id'),
            $nav_modal = $($iwptp.find('.iwptp-nav-modal-tpl').html()),
            $filters = $iwptp.find('.iwptp-filter').not('[data-iwptp-filter="sort_by"]'),
            $search = $iwptp.find('.iwptp-search-wrapper'),
            $sort = $iwptp.find('[data-iwptp-filter="sort_by"].iwptp-filter'),
            radios = {};

        $('.iwptp-nm-sort-placeholder', $nav_modal).replaceWith($sort.clone());
        $('.iwptp-nm-filters-placeholder', $nav_modal).replaceWith($search.clone().add($filters.clone()));

        if (modal_type == 'sort') {
            $nav_modal.addClass('iwptp-show-sort').removeClass('iwptp-show-filters');
        } else { // filter
            $nav_modal.addClass('iwptp-show-filters').removeClass('iwptp-show-sort');
        }

        // record radios
        $iwptp.find('input[type=radio]:checked').each(function() {
            var $this = $(this);
            radios[$this.attr('name')] = $this.val();
        })
        $nav_modal.data('iwptp-radios', radios);

        // ':' at the end of row labels
        $nav_modal.find('.iwptp-filter.iwptp-options-row > .iwptp-filter-heading > .iwptp-options-heading > .iwptp-item-row > .iwptp-text:last-child').each(function() {
            var $this = $(this),
                text = $this.text().trim();

            if (text.substr(-1) === ':') {
                $this.text(text.substr(0, text.length - 1));
            }
        })

        // duplicate header
        // if( ! $nav_modal.find('.iwptp-nm-heading--sticky').length ){
        //   var $header = $nav_modal.find('.iwptp-nm-heading');
        //   $header.clone().addClass('iwptp-nm-heading--sticky').insertAfter( $header );
        // }

        // append
        $('body')
            .addClass('iwptp-nav-modal-on')
            .append($nav_modal);

        // multirange
        $('.iwptp-range-slider-wrapper', $nav_modal).each(function() {
            var $this = $(this),
                $original = $this.children('.original'),
                $ghost = $this.children('.ghost'),
                $new_slider = $('<input/>').attr({
                    'type': 'range',
                    'class': 'iwptp-range-slider',
                    'min': $original.attr('min'),
                    'max': $original.attr('max'),
                    'step': $original.attr('step'),
                    'value': $original.attr('data-iwptp-initial-value'),
                });

            $original.add($ghost).remove();

            $this.append($new_slider);
            iwptp__multirange($new_slider[0]);
        });

        // apply
        //-- filter
        $nav_modal.find('.iwptp-nm-apply').on('click', function() {
                var $nav_clone = $nav_modal.clone();

                nav_clone_operations($nav_clone);

                var query = $('<form>').append($nav_clone).serialize(),
                    $container = $('#' + iwptp_id);

                $nav_modal.remove();
                $('body').removeClass('iwptp-nav-modal-on');
                $container[0].scrollIntoView();

                attempt_ajax($container, query, false, 'filter');
            })
            //-- sort
        $nav_modal.filter('.iwptp-show-sort').on('change', function() {
            var query = $('<form>').append($nav_modal.clone()).serialize(),
                $container = $('#' + iwptp_id);

            $nav_modal.trigger('iwptp_close');

            attempt_ajax($container, query, false, 'filter');
        })

        // clear
        $nav_modal.find('.iwptp-nm-reset').on('click', function() {
            var query = $('<form>').append($nav_modal.clone()).serialize(),
                $container = $('#' + iwptp_id),
                query = '';

            $nav_modal.trigger('iwptp_close');

            attempt_ajax($container, query, false, 'filter');
        })

        // close
        $nav_modal.find('.iwptp-nm-close').on('click', function(e) {
            e.preventDefault();

            var $container = $('#' + iwptp_id),
                radios = $.extend({}, $nav_modal.data('iwptp-radios'));

            $nav_modal.trigger('iwptp_close');

            $.each(radios, function(name, val) {
                $iwptp.find('input[type=radio][name="' + name + '"][value="' + val + '"]').each(function() {
                    $(this).prop('checked', 'checked');
                })
            })

        })

        // scroll fix
        var prev_y = false;

        $('.iwptp-nav-modal')
            .on('touchstart', function(e) {
                prev_y = e.originalEvent.touches[0].clientY;
            })
            .on('touchmove', function(e) {
                if (
                    (
                        e.originalEvent.touches[0].clientY > prev_y &&
                        !this.scrollTop
                    ) ||
                    (
                        e.originalEvent.touches[0].clientY < prev_y &&
                        this.scrollTop === (this.scrollHeight - this.offsetHeight)
                    )
                ) {
                    e.preventDefault();
                }
            })

    }

    $('body').on('iwptp_close', '.iwptp-nav-modal', function() {
        var $this = $(this),
            table_id = $this.attr('data-iwptp-table-id'),
            $container = $('#iwptp-' + table_id);

        $this.remove();
        $('body').removeClass('iwptp-nav-modal-on');
        $container[0].scrollIntoView();
    })

    // row option change ev
    $('body').on('change', '.iwptp-options-row .iwptp-option input', function() {
        var $this = $(this),
            $label = $this.closest('.iwptp-option ');

        if ($this.is(':radio')) {
            if (this.checked) {
                $label
                    .addClass('iwptp-active')
                    .siblings().removeClass('iwptp-active');
            }
        } else { // checkbox
            if (this.checked) {
                $label.addClass('iwptp-active');
            } else {
                $label.removeClass('iwptp-active');
            }
        }
    })

    // toggle
    $('body').on('click', '.iwptp-tg-trigger', function() {
        var $this = $(this),
            $toggle = $this.closest('.iwptp-toggle'),
            $table = $this.closest('.iwptp-table'),
            ft = $table.data('freezeTable');
        $toggle.toggleClass(' iwptp-tg-on iwptp-tg-off ');
        if (ft) {
            $table.freezeTable('cell_resize');
        }

    })

    $('body').on('click', '.iwptp-rn-filter, .iwptp-rn-sort', nav_modal);

    $('body').on('click', '.iwptp-accordion-heading', function() {
        $(this).closest('.iwptp-accordion').toggleClass('iwptp-open');
    });

    // apply filters
    $('body').on('click', '.iwptp-apply', function() {
        $(this).closest('.iwptp-navigation').trigger('change');
    });

    // photoswipe
    function init_photoswipe($this, index, append_item) {
        // photoswipe gallery
        if (
            typeof PhotoSwipe !== 'undefined' &&
            typeof PhotoSwipeUI_Default !== 'undefined'
        ) {
            var items = JSON.parse($this.attr('data-iwptp-photoswipe-items')),
                index = typeof index == "undefined" ? 0 : parseInt(index);

            // append items
            if (append_item) {
                items.push(append_item);
            }

            var index_src = items[index].src;

            // remove duplicates
            var unique_src = [],
                _items = [];

            $.each(items, function(index2, item) {
                if (-1 === $.inArray(item.src, unique_src)) {
                    _items.push(item);
                    unique_src.push(item.src);
                }
            })
            items = _items;

            var options = JSON.parse($this.attr('data-iwptp-photoswipe-options')),
                photoswipe = new PhotoSwipe($('.pswp')[0], PhotoSwipeUI_Default, items, options);

            photoswipe.init();

            // reposition index in case item was deleted
            $.each(items, function(index3, item) {
                if (item.src === index_src) {
                    index = index3;
                }
            })

            photoswipe.goTo(index);

            var $body = $('body');

            $body.addClass('iwptp-photoswipe-visible');
            photoswipe.listen('close', function() {
                setTimeout(function() { // fix photoswipe click through bug
                    $body.removeClass('iwptp-photoswipe-visible');
                }, 10);
            });

            $(photoswipe.container).data('iwptp_photoswipe', photoswipe);

            return true;

        } else {
            return false;
        }
    }

    // photoswipe click close on phones
    $('body').on('click', '.pswp__container', function(e) {
        var $this = $(this),
            photoswipe = $this.data('iwptp_photoswipe');

        if (
            window.innerWidth < 720 &&
            photoswipe
        ) {
            var $target = $(e.target);
            if (!$target.closest('.pswp__button').length) {
                photoswipe.close();
            }
        }
    })

    // gallery strip

    // -- image
    $('body').on('click', '.iwptp-gallery__item', function() {
        var $this = $(this),
            index = parseInt($this.attr('data-iwptp-gallery-item')),
            $gallery = $this.closest('.iwptp-gallery');

        if (!$gallery.hasClass('iwptp-gallery--include-featured')) {
            index += 1;
        }

        init_photoswipe($gallery, index);
    })

    // -- link
    $('body').on('click', '.iwptp-gallery a', function(e) {
        e.preventDefault();

        var $this = $(this),
            $gallery = $this.closest('.iwptp-gallery');

        init_photoswipe($gallery);
    })

    // image lightbox
    $('body').on('click', '.iwptp-lightbox-enabled', function() {
        destroy_offset_zoom_containers();

        var $this = $(this);
        if (
            $this.closest('.iwptp').hasClass('iwptp-quick-view-trigger--product-image') &&
            !$this.closest('.iwptp-row').hasClass('iwptp-quick-view-trigger__disabled-for-product')
        ) {
            return;
        }

        var index = 0,
            $row = get_product_rows($this),
            src = $this.attr('data-iwptp-lightbox'),
            pswp_items = JSON.parse($this.attr('data-iwptp-photoswipe-items')),
            append_item = false;

        if (
            $row.attr('data-iwptp-type') === 'variable' &&
            $row.data('iwptp_variation_selected')
        ) {
            var variation = $row.data('iwptp_variation'),
                src = variation.image.full_src,
                found = false;

            $.each(pswp_items, function(_index, item) {
                if (item.src == src) {
                    index = _index;
                    found = true;
                    return false;
                }
            })

            if (!found) {
                append_item = {
                    src: variation.image.full_src,
                    w: variation.image.full_src_w,
                    h: variation.image.full_src_h,
                    title: variation.image.title
                }

                index = pswp_items.length;
            }

        } else {
            // get starting index
            if ($this.attr('data-iwptp-photoswipe-items')) {
                $.each(pswp_items, function(_index, item) {
                    if (item.src == src) {
                        index = _index;
                        return false;
                    }
                })
            }
        }

        if (!init_photoswipe($this, index, append_item)) {
            var $el = $('<div class="iwptp-lightbox-screen"><div class="iwptp-lightbox-loader"></div><div class="iwptp-lightbox-close"></div><img class="iwptp-lightbox-image" src="' + src + '"></div>');
            $('body').append($el);
            $el.on('click ', function() {
                $el.remove();
            })
        }
    })

    // image zoom
    //-- image hover
    $('body').on('mouseenter', '.iwptp-zoom-enabled[data-iwptp-zoom-trigger="image_hover"]', function() {
            var $this = $(this),
                level = $this.attr('data-iwptp-zoom-level');
            if (!level) {
                level = '1.5';
            }

            if ($this.closest('.iwptp-device-tablet, .iwptp-device-phone').length) {
                return;
            }

            $this.css({
                'transform': 'scale(' + level + ')',
                'z-index': '2',
            })

            $this.one('mouseleave', function() {
                $this.css({
                    'transform': '',
                    'z-index': '',
                })
            })
        })
        //-- row hover
    $('body').on('mouseenter', '.iwptp-row', function() {
        var $row = $(this);
        $row.find('.iwptp-zoom-enabled[data-iwptp-zoom-trigger="row_hover"]').each(function() {
            var $zoom_me = $(this),
                level = $zoom_me.attr('data-iwptp-zoom-level');
            if (!level) {
                level = '1.5';
            }

            if ($zoom_me.closest('.iwptp-device-tablet, .iwptp-device-phone').length) {
                return;
            }

            $zoom_me.css({
                'transform': 'scale(' + level + ')',
                'z-index': '2',
            })

            $row.one('mouseleave', function() {
                $zoom_me.css({
                    'transform': '',
                    'z-index': '',
                })
            })
        })
    })

    // product image offset zoom
    // -- attach hover handler
    $('body').on('mouseenter.iwptp_offset_zoom', '.iwptp-product-image-wrapper--offset-zoom-enabled, .iwptp-gallery--offset-zoom-enabled .iwptp-gallery__item-wrapper', function(e) {
            var $this = $(this),
                src = $this.attr('data-iwptp-offset-zoom-image-src'),
                $offset_zoom = $('<div class="iwptp-offset-zoom-container ' + $this.attr('data-iwptp-offset-zoom-image-html-class') + '"><img src="' + src + '" class="iwptp-offset-zoom-container__image" /></div>'),
                $iwptp = $this.closest('.iwptp');

            if ($this.closest('.frzTbl--grab-and-scroll--grabbing').length) {
                return;
            }

            destroy_offset_zoom_containers();

            $iwptp
                .append($offset_zoom)
                .on('mousemove.iwptp_offset_zoom', function(e) {
                    position_offset_zoom_container(e, $offset_zoom, $this);
                })

            $this.on('mouseleave', destroy_offset_zoom_containers);
        })
        // -- turn off hover handler on touch screens
    $('body').on('touchstart', function() {
        $('body').off('mouseenter.iwptp_offset_zoom')
    })

    function position_offset_zoom_container(e, $offset_zoom, $trigger) {
        var left = e.originalEvent.clientX + 40,
            top = e.originalEvent.clientY,
            position = 'right';

        $offset_zoom.css({
            'left': left,
            'top': top,
        });

        var rect = $offset_zoom.get(0).getBoundingClientRect(),
            viewport_width = window.innerWidth || document.documentElement.clientWidth,
            viewport_height = window.innerHeight || document.documentElement.clientHeight;

        if (rect.right > viewport_width) {
            position = 'left';
        }

        if (position == 'left') {
            left = $trigger.get(0).getBoundingClientRect().left - 40 - rect.width;
        }

        if (rect.top < 0) {
            top = 0 + .25 * rect.height;
        } else if (rect.bottom > viewport_height) {
            top -= rect.bottom - viewport_height;
        }

        $offset_zoom.css({
            'left': left,
            'top': top,
        });
    }

    function destroy_offset_zoom_containers() {
        $('.iwptp-offset-zoom-container').remove();
        $body.off('mousemove.iwptp_offset_zoom');
    }

    // uncheck variation radio
    $('body').on('click', '.iwptp-variation-radio', function(e) {
        var $this = $(this),
            $variation = $this.closest('.iwptp-select-variation'),
            $row = $this.closest('.iwptp-row');

        if (
            $variation.hasClass('iwptp-selected') &&
            window.navigator.userAgent.indexOf("Edge") == -1
        ) {
            $this.prop('checked', false);
            $this.change();

            $row.trigger('select_variation', {
                variation_id: false,
                complete_match: false,
                attributes: false,
                variation: false,
                variation_found: false,
                variation_selected: false,
                variation_available: false,
            });
        }
    })

    // variation selected class toggle
    $('body').on('change', '.iwptp-variation-radio', function() {
        var $this = $(this),
            $others = $('.iwptp-variation-radio[name="' + $(this).attr('name') + '"]').not($(this)),
            $variation = $this.closest('.iwptp-select-variation');

        if ($this.is(':checked')) {
            $variation.addClass('iwptp-selected');
        } else {
            $variation.removeClass('iwptp-selected');
        }

        $others.not(':checked').closest('.iwptp-select-variation').removeClass('iwptp-selected');
    })

    // select variation (main) 
    //-- sync
    $('body').on('select_variation', '.iwptp-product-type-variable', function(e, data) {
        var $row = get_product_rows($(this));

        // update dropdown
        var $variation_dropdown = $row.find('.iwptp-select-variation-dropdown');
        $variation_dropdown.val(data.variation_id ? data.variation_id : '');

        // update radio
        $row.find('.iwptp-variation-radio[value="' + data.variation_id + '"]').prop('checked', true);

        // update form 
        $row.find('.variations_form').each(function() {
            var $this = $(this);
            current_variation_id = $('.variation_id', $this).val();

            if (data.variation_id != current_variation_id) {

                window.iwptp_form_reset_flag = true;
                $('.reset_variations', $this).trigger('click.wc-variation-form');
                window.iwptp_form_reset_flag = false;

                // select variation in form
                if (data.variation_id) {
                    $('.variations select', $this).each(function() {
                        var $this = $(this),
                            name = $this.attr('name');

                        if (typeof data.attributes[name] !== 'undefined') {
                            $this.val(data.attributes[name]);
                        } else {
                            $this.val('');
                        }
                    });

                    $this.trigger('check_variations');

                }

            }

        })

        // update row
        $row.data('iwptp_variation', data.variation);
        $row.data('iwptp_variation_id', data.variation_id);
        $row.data('iwptp_complete_match', data.complete_match);
        $row.data('iwptp_attributes', data.attributes);
        $row.data('iwptp_variation_found', data.variation_found);
        $row.data('iwptp_variation_selected', data.variation_selected);
        $row.data('iwptp_variation_available', data.variation_available);
        $row.data('iwptp_variation_qty', data.variation_qty);

        // update total
        update_row_total($row);
        update_table_add_selected_to_cart.call($row.get(0));

        // reset
        if (!data.variation_selected) {

            // -- add to cart buton      
            var $button = $row.find('[data-iwptp-link-code^="cart"]');

            // -- -- no variation selected
            if (
                $row.find('.iwptp-add-to-cart-wrapper').length || // cart form
                $variation_dropdown.length || // select variation -- dropdown
                $row.find('.iwptp-variation-radio').length // select variation -- radio
            ) {
                disable_button($button, 'iwptp-no-variation-selected');
            }

            // -- -- out of stock      
            if ($row.hasClass('iwptp-all-variations-out-of-stock')) {
                disable_button($button, 'iwptp-all-variations-out-of-stock');
            } else {
                enable_button($button, 'iwptp-all-variations-out-of-stock');
            }

            // -- checkbox
            $row.first().trigger('_iwptp_checkbox_change', false);

            // -- qty input
            var $qty = $row.find('.iwptp-quantity input[type=number].qty');

            if ($qty.length) {
                $qty.each(function() {
                    var $this = $(this),
                        inital_value = $this.attr('data-iwptp-initial-value'),
                        min = $this.attr('min') ? $this.attr('min') : 1,
                        value = "";

                    if (inital_value == 'min') {
                        value = min;

                    } else if (inital_value === '0') {
                        value = 0;

                    } else {
                        value = '';

                    }

                    if (
                        inital_value === 'min' &&
                        $this.attr('data-iwptp-reset-on-variation-change')
                    ) {
                        value = min;
                    }

                    $this
                        .attr({
                            'min': '',
                            'max': '',
                            'step': '',
                            'value': value,
                        });

                    $this.val(value);

                    limit_qty_controller($this.closest('.iwptp-quantity'));
                })
            }

            // -- product image
            var $product_image_wrapper = $('.iwptp-product-image-wrapper', $row),
                $product_image = $('.iwptp-product-image-wrapper > img:not(.iwptp-product-image-on-hover)', $row),
                $original_row = iwptp_get_original_row($row);

            if ($product_image_wrapper.length) {
                if (!$original_row.data('iwptp_default_image')) {
                    if ($product_image[0]) { // lazy load fix
                        $original_row.data('iwptp_default_image', $product_image[0].outerHTML);
                    } else {
                        handle_product_image_lazy_load($product_image_wrapper);
                    }

                } else {
                    $product_image.replaceWith($original_row.data('iwptp_default_image'));

                }

                if ($product_image_wrapper.hasClass('iwptp-lightbox-enabled')) {
                    if (!$product_image_wrapper.attr('data-iwptp-lightbox--original')) {
                        $product_image_wrapper.attr('data-iwptp-lightbox--original', $product_image_wrapper.attr('data-iwptp-lightbox'));
                    } else {
                        $product_image_wrapper.attr('data-iwptp-lightbox', $product_image_wrapper.attr('data-iwptp-lightbox--original'));
                    }
                }

                if ($product_image_wrapper.hasClass('iwptp-product-image-wrapper--offset-zoom-enabled')) {
                    if (!$product_image_wrapper.attr('data-iwptp-offset-zoom-image-src--original')) {
                        $product_image_wrapper.attr('data-iwptp-offset-zoom-image-src--original', $product_image_wrapper.attr('data-iwptp-offset-zoom-image-src'));
                    } else {
                        $product_image_wrapper.attr('data-iwptp-offset-zoom-image-src', $product_image_wrapper.attr('data-iwptp-offset-zoom-image-src--original'));
                    }
                }

            }

            // -- sku
            $row.find('.iwptp-sku').each(function() {
                var $sku = $(this),
                    sku = $sku.attr('data-iwptp-sku');
                $sku.text(sku); // revert to variable product sku
            })

            // -- product id
            $row.find('.iwptp-product-id').each(function() {
                var $product_id = $(this),
                    product_id = $product_id.attr('data-iwptp-product-id');
                $product_id.text(product_id); // revert to variable product ID
            })

            // -- price

            // -- -- iwptp price element
            $row.filter('.iwptp-product-type-variable').find('.iwptp-price.iwptp-variable-switch').each(function() {
                var $this = $(this),
                    id = $this.attr('data-iwptp-element-id'),
                    tpl = $this.attr('data-iwptp-variable-template'),
                    $html = $($('[data-iwptp-element-id=' + id + '][data-iwptp-price-type=' + tpl + ']').html()),
                    o = ['highest-price', 'lowest-price', 'sale-price', 'regular-price'];

                $.each(o, function(index, val) {
                    $('.iwptp-' + val + ' .iwptp-amount', $html).text($this.attr('data-iwptp-' + val));
                })

                $this.html($html);

                if (tpl == 'sale') {
                    $this.addClass('iwptp-product-on-sale');
                } else {
                    $this.removeClass('iwptp-product-on-sale');
                }

            })

            // -- -- default woocommerce template
            $row.filter('.iwptp-product-type-variable').find('.iwptp-variable-price-default-woocommerce-template').each(function() {
                var $this = $(this);
                $default = $('.iwptp-variable-switch__default', $this);
                $default
                    .show()
                    .next('.price').remove();
            })

            // -- on sale
            $row.filter('.iwptp-product-type-variable').find('.iwptp-on-sale.iwptp-variable-switch').each(function() {
                var $this = $(this);
                $this.addClass('iwptp-hide');
            })

            // -- availability
            $row.filter('.iwptp-product-type-variable').find('.iwptp-availability.iwptp-variable-switch').each(function() {
                var $this = $(this),
                    id = $this.attr('data-iwptp-element-id'),
                    stock = $this.attr('data-iwptp-stock'),
                    message_tpl = $this.attr('data-iwptp-message_tpl'),
                    stock_class = $this.attr('data-iwptp-stock_class'),
                    message = $('[data-iwptp-element-id=' + id + '][data-iwptp-availability-message="' + message_tpl + '"]').html();

                $this
                    .html($(message).find('.iwptp-stock-placeholder').text(stock))
                    .removeClass('iwptp-in-stock iwptp-low-stock iwptp-out-of-stock iwptp-on-backorder')
                    .addClass(stock_class)
                    .hide();
            })

            // -- stock
            $row.filter('.iwptp-product-type-variable').find('.iwptp-stock').each(function() {
                var $this = $(this),
                    stock = $this.attr('data-iwptp-stock'),
                    rules = $this.attr('data-iwptp-stock-range-labels'),
                    parsed_rules = !rules || rules == '{}' ? [] : JSON.parse(rules),
                    label = stock;

                var found_rule = false;

                if (
                    stock &&
                    parsed_rules.length
                ) {
                    $.each(parsed_rules, function(index, rule) {
                        if (
                            rule[0] <= stock &&
                            rule[1] >= stock
                        ) {
                            label = rule[2];
                            found_rule = true;
                        }
                    })
                }

                if (!found_rule &&
                    stock < 0
                ) {
                    stock = '';
                    label = '';
                }

                $this.html((label + '').replace('[stock]', stock));
            })

            // -- dimensions
            $row.filter('.iwptp-product-type-variable').find('.iwptp-dimensions').each(function() {
                var $this = $(this);
                $this.html($this.attr('data-iwptp-default-dimensions'));
            })

            // -- custom field
            $row.filter('.iwptp-product-type-variable').find('.iwptp-custom-field.iwptp-variable-switch').each(function() {
                var $this = $(this),
                    element_id = $this.attr('data-iwptp-element-id'),
                    product_id = $this.closest('.iwptp-row').attr('data-iwptp-product-id'),
                    table_id = $this.closest('.iwptp').attr('data-iwptp-table-id');

                if ('undefined' !== typeof window['iwptp_' + table_id + '_variable_switch_cf']) {
                    var cf_vals = window['iwptp_' + table_id + '_variable_switch_cf'];
                    if ('undefined' !== typeof cf_vals[element_id]) {
                        $this.html(cf_vals[element_id][product_id])
                    }
                }
            })

        }

        if (!$.isEmptyObject(data.variation)) {

            // -- add to cart buton      
            var $button = $row.find('[data-iwptp-link-code^="cart"]');

            // -- -- no variation selected
            enable_button($button, 'iwptp-no-variation-selected');

            // -- -- out of stock
            if (data.variation.is_in_stock) {
                enable_button($button, 'iwptp-variation-out-of-stock');
            } else {
                disable_button($button, 'iwptp-variation-out-of-stock');
            }

            // -- qty input
            var $qty = $row.find('.iwptp-quantity input[type=number].qty');

            if ($qty.length) {

                $qty.each(function() {
                    var $this = $(this),
                        $qty_wrapper = $this.closest('.iwptp-quantity'),
                        inital_value = $this.attr('data-iwptp-initial-value'),
                        min = data.variation.min_qty ? parseFloat(data.variation.min_qty) : 1,
                        max = data.variation.max_qty ? parseFloat(data.variation.max_qty) : '',
                        step = data.variation.step ? parseFloat(data.variation.step) : '',
                        value = "";

                    // validate val
                    var current_val = parseFloat($this.val());
                    if (
                        current_val &&
                        current_val !== NaN
                    ) { // neither empty, nor 0
                        if (current_val < min) {
                            value = min;
                        } else if (
                            max &&
                            current_val > max
                        ) {
                            value = max;
                        } else {
                            value = current_val;
                        }
                    }

                    if (
                        inital_value === 'min' &&
                        $this.attr('data-iwptp-reset-on-variation-change')
                    ) {
                        value = min;
                    }

                    var iwptp_min = min;

                    if (inital_value === '0') {
                        min = '0';
                    }

                    $this.attr({
                        'value': value,
                        'min': min,
                        'data-iwptp-min': iwptp_min,
                        'max': max,
                        'step': step
                    });

                    $this.val(value);

                    $.each(['min', 'max', 'step'], function(index, attr) {
                        $qty_wrapper.find('.iwptp-quantity-error-placeholder--' + attr).text($this.attr(attr));
                    })

                    $this.trigger('change');
                })

            }

            // -- qty select
            var $select = $row.find('.iwptp-quantity > select.iwptp-qty-select');
            if ($select.length) {

                // re-create select
                var qty_label = $select.attr('data-iwptp-qty-label'),
                    max_qty = parseInt($select.attr('data-iwptp-max-qty')),
                    val = data.variation.min_qty,
                    options = '<option value="' + data.variation.min_qty + '" selected="selected">' + qty_label + data.variation.min_qty + '</option>';
                if (data.variation.max_qty) {
                    max_qty = data.variation.max_qty;
                }

                while (val < max_qty) {
                    val += data.variation.step || 1;
                    options += '<option>' + val + '</option>';
                }
                $select.html(options);
                $select.attr('min', data.variation.min_qty)
            }

            // -- product image
            var $product_image_wrapper = $('.iwptp-product-image-wrapper', $row),
                $product_image = $('.iwptp-product-image-wrapper > img:not(.iwptp-product-image-on-hover)', $row),
                $original_row = iwptp_get_original_row($row);

            if ($product_image[0]) {

                if (!$original_row.data('iwptp_default_image')) {
                    $original_row.data('iwptp_default_image', $product_image[0].outerHTML);
                }

                if (
                    $product_image.length &&
                    data.variation.image &&
                    data.variation.image.src
                ) {

                    $product_image.attr({
                        'src': data.variation.image.src,
                        'srcset': data.variation.image.srcset ? data.variation.image.srcset : '',
                    });

                    if ($product_image_wrapper.hasClass('iwptp-lightbox-enabled')) {
                        if (!$product_image_wrapper.attr('data-iwptp-lightbox--original')) {
                            $product_image_wrapper.attr('data-iwptp-lightbox--original', $product_image_wrapper.attr('data-iwptp-lightbox'));
                        }

                        $product_image_wrapper.attr('data-iwptp-lightbox', data.variation.image.full_src);
                    }

                    if ($product_image_wrapper.hasClass('iwptp-product-image-wrapper--offset-zoom-enabled')) {
                        if (!$product_image_wrapper.attr('data-iwptp-offset-zoom-image-src--original')) {
                            $product_image_wrapper.attr('data-iwptp-offset-zoom-image-src--original', $product_image_wrapper.attr('data-iwptp-offset-zoom-image-src'));
                        }

                        $product_image_wrapper.attr('data-iwptp-offset-zoom-image-src', data.variation.image.full_src);
                    }
                }

            } else {
                handle_product_image_lazy_load($product_image_wrapper);

            }

            // -- sku
            if (data.variation.sku) {
                $row.find('.iwptp-sku').each(function() {
                    var $this = $(this);
                    if ($this.hasClass('iwptp-variable-switch')) {
                        $this.text(data.variation.sku);
                    }
                })
            }

            // -- product id
            if (data.variation.variation_id) {
                $row.find('.iwptp-product-id').each(function() {
                    var $this = $(this);
                    if ($this.hasClass('iwptp-variable-switch')) {
                        $this.text(data.variation.variation_id);
                    }
                })
            }

            // -- price

            // -- -- iwptp price element      
            $row.filter('.iwptp-product-type-variable').find('.iwptp-price.iwptp-variable-switch').each(function() {
                var $this = $(this),
                    id = $this.attr('data-iwptp-element-id'),
                    tpl = parseFloat(data.variation.display_price) < parseFloat(data.variation.display_regular_price) ? 'sale' : 'regular',
                    $html = $($('[data-iwptp-element-id=' + id + '][data-iwptp-price-type=' + tpl + ']').html());

                $html
                    .find('.iwptp-regular-price .iwptp-amount').text(data.variation.display_regular_price)
                    .end()
                    .find('.iwptp-sale-price .iwptp-amount').text(data.variation.display_price);
                $this.html($html);

                if (tpl == 'sale') {
                    $this.addClass('iwptp-product-on-sale');
                } else {
                    $this.removeClass('iwptp-product-on-sale');
                }
            })

            // -- -- default woocommerce template
            $row.filter('.iwptp-product-type-variable').find('.iwptp-variable-price-default-woocommerce-template').each(function() {
                var $this = $(this);
                $default = $('.iwptp-variable-switch__default', $this);

                if (!data.variation.price_html) { // single variation won't have price html
                    return;
                }

                $default
                    .hide()
                    .nextAll('.price').remove()
                    .end()
                    .after(data.variation.price_html);
            })

            // -- on sale
            $row.filter('.iwptp-product-type-variable').find('.iwptp-on-sale.iwptp-variable-switch').each(function() {
                var $this = $(this),
                    precision = $this.attr('data-iwptp-precision'),
                    is_on_sale = parseFloat(data.variation.display_price) < parseFloat(data.variation.display_regular_price),
                    $price_diff = $this.find('.iwptp-on-sale__price-diff'),
                    $percent_diff = $this.find('.iwptp-on-sale__percent-diff');

                if (is_on_sale) {
                    $this.removeClass('iwptp-hide');

                    price_diff = data.variation.display_regular_price - data.variation.display_price;
                    percent_diff = parseFloat(((price_diff / data.variation.display_regular_price) * 100).toFixed(precision));

                    $price_diff.html(format_price(price_diff));
                    $percent_diff.text(percent_diff);
                } else {
                    $this.addClass('iwptp-hide');
                }
            })

            // -- availability
            $row.filter('.iwptp-product-type-variable').find('.iwptp-availability.iwptp-variable-switch').each(function() {
                var $this = $(this),
                    id = $this.attr('data-iwptp-element-id'),
                    message_tpl = '',
                    stock_class = '',

                    out_of_stock_message = $('[data-iwptp-element-id=' + id + '][data-iwptp-availability-message="out_of_stock_message"]').html(),
                    low_stock_message = $('[data-iwptp-element-id=' + id + '][data-iwptp-availability-message="low_stock_message"]').html(),
                    single_stock_message = $('[data-iwptp-element-id=' + id + '][data-iwptp-availability-message="single_stock_message"]').html(),
                    in_stock_message = $('[data-iwptp-element-id=' + id + '][data-iwptp-availability-message="in_stock_message"]').html(),
                    in_stock_managed_message = $('[data-iwptp-element-id=' + id + '][data-iwptp-availability-message="in_stock_managed_message"]').html(),
                    on_backorder_message = $('[data-iwptp-element-id=' + id + '][data-iwptp-availability-message="on_backorder_message"]').html(),
                    on_backorder_managed_message = $('[data-iwptp-element-id=' + id + '][data-iwptp-availability-message="on_backorder_managed_message"]').html(),

                    low_stock_threshold = $this.attr('data-iwptp-low_stock_threshold');

                $this.show();

                if (!data.variation.is_in_stock) {
                    message_tpl = 'out_of_stock_message';
                    stock_class = "iwptp-out-of-stock";

                } else if (
                    (
                        (
                            data.variation.managing_stock &&
                            data.variation.is_on_backorder &&
                            data.variation.backorders_require_notification
                        ) ||
                        (!data.variation.managing_stock &&
                            data.variation.is_on_backorder
                        )
                    ) &&
                    on_backorder_message
                ) {
                    message_tpl = 'on_backorder_message';
                    stock_class = "iwptp-on-backorder";

                } else if (data.variation.managing_stock) { // in stock, managed
                    message_tpl = 'in_stock_managed_message';
                    stock_class = "iwptp-in-stock";

                    if (!data.variation.backorders_allowed) {

                        if ( // low stock	
                            data.variation.stock == 1 &&
                            single_stock_message
                        ) {
                            message_tpl = 'single_stock_message';
                            stock_class = "iwptp-low-stock";

                        } else if ( // single stock
                            low_stock_message &&
                            low_stock_threshold &&
                            data.variation.stock <= low_stock_threshold
                        ) {
                            message_tpl = 'low_stock_message';
                            stock_class = "iwptp-low-stock";

                        }

                    } else if (
                        data.variation.backorders_require_notification &&
                        on_backorder_managed_message
                    ) { // backorder allowed, managed stock, greater than 0
                        message_tpl = 'on_backorder_managed_message';
                        stock_class = "iwptp-on-backorder";

                    } else if (data.variation.stock <= 0) { // backorder allowed, managed stock, 0 or less
                        message_tpl = 'on_backorder_message';
                        stock_class = "";
                        $this.hide();

                    } else { // backorder allowed, managed stock, greater than 0
                        message_tpl = 'in_stock_message';
                        stock_class = "iwptp-in-stock";
                    }

                } else { // in stock, not managed
                    message_tpl = '';
                    stock_class = '';

                    if (in_stock_message) {
                        message_tpl = 'in_stock_message';
                        stock_class = "iwptp-in-stock";
                    }
                }

                var $message = $($('[data-iwptp-element-id=' + id + '][data-iwptp-availability-message="' + message_tpl + '"]').html());

                $this
                    .html($message.find('.iwptp-stock-placeholder').text((data.variation.stock ? data.variation.stock : '')).end())
                    .removeClass('iwptp-in-stock iwptp-low-stock iwptp-out-of-stock iwptp-on-backorder')
                    .addClass(stock_class);

            })

            // -- stock
            $row.filter('.iwptp-product-type-variable').find('.iwptp-stock.iwptp-variable-switch').each(function() {
                var $this = $(this),
                    stock = data.variation.stock,
                    rules = $this.attr('data-iwptp-stock-range-labels'),
                    parsed_rules = !rules || rules == '{}' ? [] : JSON.parse(rules),
                    label = stock;

                if (!label ||
                    label == NaN
                ) {
                    $this.hide();
                    return;
                } else {
                    $this.show();
                }

                var found_rule = false;
                if (
                    stock &&
                    parsed_rules.length
                ) {
                    $.each(parsed_rules, function(index, rule) {
                        if (
                            rule[0] <= stock &&
                            rule[1] >= stock
                        ) {
                            label = rule[2];
                            found_rule = true;
                        }
                    })
                }

                if (!found_rule &&
                    stock < 0
                ) {
                    stock = '';
                    label = '';
                }

                $this.html((label + '').replace('[stock]', stock));
            })

            // -- dimensions
            $row.filter('.iwptp-product-type-variable').find('.iwptp-dimensions.iwptp-variable-switch').each(function() {
                var $this = $(this);
                $this.html(data.variation.dimensions_html);
            })

            // -- custom field
            $row.filter('.iwptp-product-type-variable').find('.iwptp-custom-field.iwptp-variable-switch').each(function() {
                var $this = $(this),
                    element_id = $this.attr('data-iwptp-element-id'),
                    table_id = $this.closest('.iwptp').attr('data-iwptp-table-id');

                if ('undefined' !== typeof window['iwptp_' + table_id + '_variable_switch_cf']) {
                    var cf_vals = window['iwptp_' + table_id + '_variable_switch_cf'];
                    if ('undefined' !== typeof cf_vals[element_id]) {
                        $this.html(cf_vals[element_id][data.variation.variation_id])
                    }
                }
            })

        }

        var $freeze_table_container = $row.closest('.frzTbl'),
            $freeze_table = $freeze_table_container.find('.frzTbl-table');
        if ($freeze_table.length) {
            $freeze_table.freezeTable('cell_resize');
        }

    })

    //-- -- update from dropdown
    $('body').on('change', '.iwptp-select-variation-dropdown', function(e) {
        var $this = $(this),
            $selected = $this.find('option:selected'),
            $row = $this.closest('.iwptp-row');

        $row.trigger('select_variation', {
            variation_id: $this.val(),
            complete_match: $selected.hasClass('iwptp-complete_match'),
            attributes: $selected.attr('data-iwptp-attributes') ? JSON.parse($selected.attr('data-iwptp-attributes')) : '',
            variation: $selected.attr('data-iwptp-variation') ? JSON.parse($selected.attr('data-iwptp-variation')) : '',
            variation_found: !!$selected.attr('value'),
            variation_selected: !!$selected.attr('value'),
            variation_available: !$selected.is(':disabled') && !!$selected.attr('value'),
        });
    })

    //-- -- update from radio
    $('body').on('change', '.iwptp-variation-radio', function(e) {
        var $this = $(this),
            $wrapper = $this.closest('.iwptp-select-variation'),
            $row = $this.closest('.iwptp-row');

        if ($this.is(':checked')) {
            $row.trigger('select_variation', {
                variation_id: $this.val(),
                complete_match: $wrapper.hasClass('iwptp-complete_match'),
                attributes: JSON.parse($wrapper.attr('data-iwptp-attributes')),
                variation: JSON.parse($wrapper.attr('data-iwptp-variation')),
                variation_found: true,
                variation_selected: true,
                variation_available: !$this.is(':disabled'),
            });
        }
    })

    //-- -- update from form
    $('body').on('woocommerce_variation_has_changed', '.iwptp-row .variations_form', function(e) {
        get_select_variation_from_cart_form($(this));
    })

    function get_select_variation_from_cart_form($form) {
        if (window.iwptp_form_reset_flag) { // avoid infinite loop
            return;
        }

        var variations = JSON.parse($form.attr('data-product_variations')),
            $row = $form.closest('.iwptp-row'),
            $variation_id = $('.variation_id', $form),
            variation = {},
            attributes = {},
            selected_variation = $variation_id.val();

        $.each(variations, function(index, value) {
            if (parseInt(value['variation_id']) == selected_variation) {
                variation = value;
                return false;
            }
        })

        var variation_selected = true;
        $('.variations select', $form).each(function() {
            var $this = $(this);
            attributes[$this.attr('name')] = $this.val();

            if (!$this.val()) {
                variation_selected = false;
            }
        })

        var variation_available = false;
        if (
            variation &&
            !$.isEmptyObject(variation) &&
            variation.is_purchasable &&
            variation.is_in_stock &&
            variation.variation_is_visible
        ) {
            variation_available = true;
        }

        $row.trigger('select_variation', {
            variation: variation,
            variation_id: selected_variation,
            complete_match: true,
            attributes: attributes,
            variation_found: !!selected_variation,
            variation_selected: variation_selected,
            variation_available: variation_available,
        });

    }

    // prepare variation options
    // (gets called by 'after_every_load()')
    function prep_variation_options($container) {
        $('.iwptp-product-type-variable', $container).each(function() {
            var $row = $(this),
                $dropdown = $('.iwptp-select-variation-dropdown', $row),
                $radio = $('.iwptp-variation-radio', $row),
                $form = $('.variations_form', $row),
                $options = $dropdown.add($radio).add($form);

            // flag availability of options in row
            if ($options.length) {
                $row.data('iwptp_variation_ops', true);
            }

            // trigger 'select_variation'

            if ($form.length) {
                $form.each(function() {
                    var $form = $(this);

                    setTimeout(function() { // need to match setTimeout in add-to-cart-variation.js that delays form init
                        // this init syncs the WC form with IWPTPL native controls including add-to-cart button
                        $form.find('select').first().change();
                    }, 200);
                })

            } else if ($dropdown.length) {
                $dropdown.trigger('change');

            } else if ($radio.length) {
                $radio.filter(':checked').trigger('change');

            }

        })
    }

    // lazy loaded product image
    function handle_product_image_lazy_load($product_image_wrapper) {
        if (
            $product_image_wrapper.length &&
            !$product_image_wrapper.hasClass('iwptp-awaiting-image-lazy-load')
        ) {
            $product_image_wrapper.addClass('iwptp-awaiting-image-lazy-load');

            $product_image_wrapper[0].addEventListener(
                'load',
                function(event) {
                    if (
                        event.target.tagName === 'IMG' &&
                        $product_image_wrapper.hasClass('iwptp-awaiting-image-lazy-load')
                    ) {
                        $product_image_wrapper.removeClass('iwptp-awaiting-image-lazy-load');

                        var $row = iwptp_get_original_row($product_image_wrapper.closest('.iwptp-row')),
                            data = $row.data(),
                            $product_image = $product_image_wrapper.children('img');

                        $row.data('iwptp_default_image', $product_image[0].outerHTML);

                        if (!$.isEmptyObject(data.iwptp_variation) &&
                            $product_image.length &&
                            data.iwptp_variation.image &&
                            data.iwptp_variation.image.src
                        ) {
                            $product_image.attr({
                                'src': data.iwptp_variation.image.src,
                                'srcset': data.iwptp_variation.image.srcset ? data.iwptp_variation.image.srcset : ''
                            });

                            if ($product_image_wrapper.hasClass('iwptp-lightbox-enabled')) {
                                $product_image_wrapper.attr('data-iwptp-lightbox', data.iwptp_variation.image.full_src);
                            }

                            if ($product_image_wrapper.hasClass('iwptp-product-image-wrapper--offset-zoom-enabled')) {
                                $product_image_wrapper.attr('data-iwptp-offset-zoom-image-src', data.variation.image.full_src);
                            }

                        }
                    }
                },
                true
            );

        }
    }

    // quantity controller

    // -- touchscreens - automatically move cursor to end of input
    $('body').on('touchend', '.iwptp-quantity .qty', function() {
        var $this = $(this),
            _ = this;
        if (!$this.is(':focus')) {
            $this.one('focus', function() {
                var val = _.value;
                _.value = '';
                setTimeout(function() {
                    _.value = val;
                }, 1);
            })
        }
    })

    // -- get rid of external influence
    if ($('.iwptp').length) { // Quantities and Units for WooCommerce breaks counter
        $(document).off('change', '.qty');
    }

    if ('ontouchstart' in document.documentElement) {
        var mousedown = 'touchstart',
            mouseup = 'touchend';
    } else {
        var mousedown = 'mousedown',
            mouseup = 'mouseup';
    }

    // -- controller mouseDown
    $('body').on(mousedown + (mousedown == 'touchstart' ? ' dblclick' : ''), '.iwptp-qty-controller:not(.iwptp-disabled)', function qty_controller_onMousedown(e) {

        $('body').addClass('iwptp-noselect--qty-increment');

        // prevent accidental zoom && extra click on phone
        if (e.type === 'dblclick') {
            e.preventDefault();
            return;
        }

        var $this = $(this),
            $parent = $this.parent(),
            $qty = $this.siblings('.qty'),
            min = $qty.attr('min') ? $qty.attr('min') : 0,
            max = $qty.attr('max') ? $qty.attr('max') : false,
            step = $qty.attr('step') ? $qty.attr('step') : 1,
            val = $qty.val() > -1 ? $qty.val() : min;

        if (!step % 1) {
            min = parseInt(min);
            max = parseInt(max);
            step = parseInt(step);
            val = parseInt(val);
        } else {
            min = parseFloat(min);
            max = parseFloat(max);
            step = parseFloat(step);
            val = parseFloat(val);
        }

        if (isNaN(val)) {
            val = 0;
        }

        if ($this.hasClass('iwptp-plus')) {
            $qty.val(((val * 1e12) + (step * 1e12)) / 1e12).change();

        } else { // iwptp-minus
            var next = ((val * 1e12) - (step * 1e12)) / 1e12;
            if (next < min) {
                next = 0;
            }
            $qty.val(next).change();

        }

        // if( e.type !== 'dblclick' ){ // the stop events won't work for a dblclick
        var count = 0,
            clear = setInterval(
                function() {
                    count++;
                    if (count % 5 || count < 50) {
                        return;
                    }

                    if ($this.hasClass('iwptp-disabled')) {
                        return;
                    }

                    var val = $qty.val() ? $qty.val() : min;
                    if (!step % 1) {
                        val = parseInt(val);
                    } else {
                        val = parseFloat(val);
                    }

                    if ($this.hasClass('iwptp-plus')) {
                        $qty.val(((val * 1e12) + (step * 1e12)) / 1e12).change();

                    } else { // iwptp-minus
                        var next = ((val * 1e12) - (step * 1e12)) / 1e12;
                        if (next < min) {
                            next = 0;
                        }
                        $qty.val(next).change();

                    }
                }, 10
            );

        $this.data('iwptp_clear', clear);

        // }

        // stop counter
        $this.one(mouseup + ' mouseout', function() {
            var $this = $(this),
                clear = $this.data('iwptp_clear');
            if (clear) {
                clearInterval(clear);
                $this.data('iwptp_clear', false);
            }

            // clear unnecesary selection
            $('body').removeClass('iwptp-noselect--qty-increment');
        })

        limit_qty_controller($parent);

    })

    // -- validator
    $('body').on('change', '.iwptp-quantity .qty', function qty_controller_validate(e, syncing) {
        var $this = $(this),
            min = $this.attr('min') ? parseFloat($this.attr('min')) : 0,
            max = $this.attr('max') ? parseFloat($this.attr('max')) : 1e12,
            initial = $this.attr('data-iwptp-initial-value'),
            step = $this.attr('step') ? parseFloat($this.attr('step')) : 1,
            val = parseFloat($this.val());

        // enforce initial
        if (!val) {
            if (initial === 'min') {
                $this.val(min);

            } else if (initial === 'empty') {
                $this.val('');

            } else if (initial === '0') {
                $this.val('0');

            }
        }

        // enforce min
        if (
            val &&
            val < min
        ) {
            $this.val(min);
        }

        // enforce max
        if (val > max) {
            $this.val(max);
        }

        // enforce step
        if (
            val &&
            (val * 1e12) % (step * 1e12)
        ) {

            var _val = val * 1e12,
                _step = step * 1e12,
                new_val = (_val + _step - ((val % step) * 1e12)) / 1e12;

            $this.val(new_val);
        }

        if (!syncing) {
            $this.trigger('change', true);
        }

        limit_qty_controller($(this).parent());
    })

    // -- click enter to add to cart
    $('body').on('keypress', '.iwptp-quantity .qty', function(e) {
        if (e.keyCode == 13) {
            var $rows = get_product_rows($(this));
            $rows.find('.iwptp-button[data-iwptp-link-code^="cart"]').eq(0).click();
        }
    });

    // -- toggle controllers
    function limit_qty_controller($qty_wrapper) {
        $qty_wrapper.each(function() {
            var $this = $(this),
                $minus = $this.children('.iwptp-minus'),
                $plus = $this.children('.iwptp-plus'),
                $qty = $this.find('.qty'),
                initial = $qty.attr('data-iwptp-initial-value'),
                min = $qty.attr('min') ? parseFloat($qty.attr('min')) : 1,
                max = $qty.attr('max') ? parseFloat($qty.attr('max')) : false,
                step = $qty.attr('step') ? parseFloat($qty.attr('step')) : 1,
                val = parseFloat($qty.val());

            if (!val ||
                isNaN(val)
            ) {
                val = 0;
            }

            if (-1 !== $.inArray(initial, ['empty', '0'])) {
                min = 0;
            }

            $minus.removeClass('iwptp-disabled');
            if (val - step < min) {
                $minus.addClass('iwptp-disabled');
            }

            $plus.removeClass('iwptp-disabled');
            if (
                false !== max &&
                val + step > max
            ) {
                $plus.addClass('iwptp-disabled');
            }
        })
    }

    // -- initial quantity
    $('body').on('iwptp_after_every_load.iwptp_initial_qty_update', '.iwptp', function() {
        $(this).find('.iwptp-quantity input[type="number"].qty').each(function() {
            var $this = $(this),
                initial_value = $this.attr('data-iwptp-initial-value');

            if (initial_value === '0') {
                $this.val(0);
            } else if (initial_value === 'empty') {
                $this.val('');
            } else if (initial_value === 'min') {
                var min = $this.attr('min') ? $this.attr('min') : 1;
                $this.val(min);
            }

            $this
                .trigger('iwptp_updating_initial_quantity')
                .trigger('change')
                .trigger('iwptp_initial_quantity_updated');

        });

    });

    // quanity error
    $('body').on('keyup change', '.iwptp-quantity .qty', function(e) {
        var $this = $(this),
            max = $this.attr('max') ? parseFloat($this.attr('max')) : 1e9,
            min = $this.attr('min') ? parseFloat($this.attr('min')) : 1,
            step = $this.attr('step') ? parseFloat($this.attr('step')) : 1,
            val = $this.val() ? parseFloat($this.val()) : 0,
            $wrapper = $this.closest('.iwptp-quantity');

        $wrapper.removeClass('iwptp-quantity-error iwptp-quantity-error--min iwptp-quantity-error--max iwptp-quantity-error--step');

        if (!val) {
            return;
        }

        if (val < min) {
            $wrapper.addClass('iwptp-quantity-error iwptp-quantity-error--min');

        } else if (val > max) {
            $wrapper.addClass('iwptp-quantity-error iwptp-quantity-error--max');

        } else if ((val * 1e12) % (step * 1e12)) {
            $wrapper.addClass('iwptp-quantity-error iwptp-quantity-error--step');
        }

        var $row = iwptp_get_sibling_rows($this.closest('.iwptp-row')),
            $button = $row.find('.iwptp-button[data-iwptp-link-code^="cart_"]');

        if ($wrapper.hasClass('iwptp-quantity-error')) {
            disable_button($button, 'iwptp-quantity-input-error');
        } else {
            enable_button($button, 'iwptp-quantity-input-error');
        }

        limit_qty_controller($wrapper);

    });

    // sync qty between sibling tables
    $('body').on('change', '.iwptp-quantity input.qty, select.iwptp-qty-select', function(e, syncing) {
        var $input = $(this),
            $product_rows = get_product_rows($input),
            $siblings = $product_rows.find('input.qty, select.iwptp-qty-select').not(this),
            val = $input.val();

        $siblings.val(val);

        if ($input.closest('.iwptp-add-to-cart-wrapper').length) {
            return;
        }

        var $wc_default_button = $('.add_to_cart_button', $product_rows);
        $wc_default_button.data('quantity', val);
        $wc_default_button.attr('data-quantity', val);

        if (!syncing) {
            syncing = true;
            $siblings.trigger('change', syncing);
        }

    })

    if (window.innerWidth < 1200) {
        $('body').on('contextmenu', '.iwptp-noselect', function() {
            return false;
        })
    }

    // total
    $('body').on('keyup mouseup change', '.iwptp-quantity .qty', function() {
        update_row_total($(this).closest('.iwptp-row'));
    });

    if ($('.iwptp_normal_left_sidebar .iwptp-left-sidebar').length > 0) {
        let width = $('.iwptp_normal_left_sidebar .iwptp-left-sidebar').width() + 10;
        $('.iwptp-table-container .iwptp-table-content').css({
            width: 'calc(100% - ' + width + 'px)'
        })
    }

    if ($('.iwptp_normal_right_sidebar .iwptp-right-sidebar').length > 0) {
        let width = $('.iwptp_normal_right_sidebar .iwptp-right-sidebar').width() + 10;
        $('.iwptp-table-container .iwptp-table-content').css({
            width: 'calc(100% - ' + width + 'px)'
        })
    }

    $(document).on('click', '.iwptp-full-screen-table-button', function() {
        $(this).closest('.iwptp-table-container').toggleClass('iwptp-full-screen-table');
    });

    $(document).on('click', '.iwptp-mini-cart-close-button', function() {
        $('body').find('.iwptp-mini-cart').removeClass('active');
    });

    $(document).on('click', '.iwptp-mini-cart-open-button', function() {
        $('body').find('.iwptp-mini-cart').addClass('active');
    });

    $(document).on('click', '.iwptp-cart-overly', function() {
        $('body').find('.iwptp-mini-cart').removeClass('active');
    });

    $(document).on('click', '.iwptp-mini-cart-toggle-button', function() {

        $('body').find('.iwptp-mini-cart').toggleClass('active');
    });

    // -- name your price: ensure qty if name your price is used
    $('body').on('keyup change', '.iwptp .iwptp-name-your-price--input', function(e) {
        var $nyp = $(this),
            nyp_val = $nyp.val(),
            $rows = iwptp_get_sibling_rows($nyp.closest('.iwptp-row')),
            $qty = $('.iwptp-quantity input.qty', $rows),
            qty_val = $qty.val(),
            $cb = $('.iwptp-cart-checkbox ', $rows);

        nyp_validate($nyp);

        // added val - add qty
        if (
            nyp_val &&
            !qty_val
        ) {
            $qty.val($qty.attr('min'));

            // removed val - remove qty
        } else if (!nyp_val &&
            qty_val
        ) {
            var inital_val_type = $qty.attr('data-iwptp-nyp-initial-value'),
                val = $qty.attr('min');

            if (inital_val_type === 'empty') {
                val = '';

            } else if (inital_val_type === '0') {
                val = '0';

            }

            $qty.val(val);
        }

        update_row_total($(this).closest('.iwptp-row'));

        if ($qty.length) {
            $qty.change();
        } else if ($cb.length) {
            $rows.trigger('iwptp_checkbox_change', !!$nyp.val());
        }

    });

    function update_row_total($row, force_qty) {
        var $rows = iwptp_get_sibling_rows($row),
            $qty = $rows.find('.qty').eq(0),
            qty = $qty.length ? parseFloat($qty.val()) : 1,
            $total = $('.iwptp-total', $rows),
            $cb = $('.iwptp-cart-checkbox ', $rows),
            prev_total = parseFloat($total.attr('data-iwptp-in-cart-total')),
            price = $rows.attr('data-iwptp-price') ? parseFloat($rows.attr('data-iwptp-price')) : 0,
            total = 0;

        if (force_qty) {
            qty = force_qty;
        }

        // variable
        if ($rows.hasClass('iwptp-product-type-variable')) {
            if (
                $rows.data('iwptp_variation_found') &&
                $rows.data('iwptp_variation')
            ) {
                price = parseFloat($rows.data('iwptp_variation').display_price.split(iwptp_params.price_decimal_separator).join('.').split(iwptp_params.price_thousand_separator).join(''));
            } else {
                price = 0;
            }
        }

        // name your price 
        if ($row.hasClass('iwptp-product-has-name-your-price')) {
            var $nyp = $('.iwptp-name-your-price--input', $rows);
            if ($nyp.filter(':visible').length) { // variable product compatibility
                price = $nyp.val() ? parseFloat($nyp.val()) : 0;
            }
        }

        total = qty * price;

        // if checkbox is unchecked and qty is not min then no total
        if (
            $cb.length &&
            !$rows.data('iwptp_checked') &&
            $qty.attr('data-iwptp-initial-value') !== 'min'
        ) {
            total = 0;
        }

        $total.each(function() {
            var $this = $(this),
                _total = total;

            if ($this.hasClass('iwptp-total--include-total-in-cart')) {
                _total = _total + prev_total;
            }

            if (_total) {
                $this
                    .removeClass('iwptp-total--empty')
                    .find('.iwptp-amount').text(format_price_figure(_total));
            } else {
                $this
                    .addClass('iwptp-total--empty');
            }
        })

        var $freeze_table_container = $row.closest('.frzTbl'),
            $freeze_table = $freeze_table_container.find('.frzTbl-table');
        if ($freeze_table.length) {
            $freeze_table.freezeTable('cell_resize');
        }

        $row.data('iwptp-total', total);
        $row.trigger('iwptp_total_updated');
    }

    // audio player
    $('body').on('click', '.iwptp-player__button', function() {
        var $button = $(this),
            $container = $button.closest('.iwptp-player'),
            src = $container.attr('data-iwptp-src'),
            loop = $container.attr('data-iwptp-loop'),
            $el = $container.data('iwptp-media-el');

        if (!$el) {
            $el = $('<audio class="iwptp-audio-elm" src="' + src + '"></audio>');
            $container.append($el);
            $container.data('iwptp-media-el', $el);

            if (loop) {
                $el.prop('loop', true);

            } else {
                $el.on('ended', function() {
                    $container.toggleClass('iwptp-player--playing');
                })
            }

        }

        if ($button.hasClass('iwptp-player__play-button')) {
            $el[0].play();

            if (!$container.hasClass('iwptp-media-loaded')) {
                $el.on('canplay', function() {
                    $container.addClass('iwptp-media-loaded');
                })
            }

            $('audio.iwptp-audio-elm').not($el).each(function() {
                this.currentTime = 0;
                this.pause();
            })

            // pause others      
            var $other_players = $('.iwptp-player.iwptp-player--playing').not($container);
            $other_players.find('.iwptp-player__pause-button').click();
        } else {
            $el[0].pause();
        }

        $container.toggleClass('iwptp-player--playing');

    })

    // term click
    // -- trigger filter
    $('body').on('click', '.iwptp-trigger_filter > [data-iwptp-slug]', function() {
            var $this = $(this),
                slug = $this.attr('data-iwptp-slug'),
                taxonomy = $this.parent().attr('data-iwptp-taxonomy'),
                $container = $this.closest('.iwptp'),
                $nav = $container.find('.iwptp-navigation'),
                $option = $nav.find('[data-iwptp-taxonomy="' + taxonomy + '"] [data-iwptp-slug="' + slug + '"]'),
                $input = $('input', $option);

            if (!$option.length) {
                return;
            }

            $nav.addClass('iwptp-force-hide-dropdown-menus');
            $input.prop('checked', !$input.prop('checked'));
            $nav.trigger('change');
        })
        // -- archive redirect
    $('body').on('click', '.iwptp-archive_redirect > [data-iwptp-slug]', function() {
        var $this = $(this),
            url = $this.attr('data-iwptp-archive-url');
        if (!$this.is('a')) {
            window.location = url;
        }
    })

    // remove
    $('body').on('click', '.iwptp-row:not(.iwptp-removing-product) .iwptp-remove:not(.iwptp-disabled)', function() {
        var $this = $(this),
            $row = $product_row = get_product_rows($this),
            product_id = $row.attr('data-iwptp-product-id'),
            variation_id = $row.attr('data-iwptp-variation-id'),
            params = {
                payload: {
                    products: {},
                    variations: {},
                    overwrite_cart_qty: true
                }
            };

        if ($this.hasClass('iwptp-refresh-enabled')) {
            params.redirect = window.location.href;
        }

        params.payload.products[product_id] = 0;

        // variation
        if (variation_id) {
            params.payload.variations[product_id] = {};
            params.payload.variations[product_id][variation_id] = 0
        }

        // variable product
        if ($row.hasClass('iwptp-product-type-variable')) {
            params.payload.variations[product_id] = $.extend({}, iwptp_cart_result_cache.in_cart[product_id]);
            $.each(params.payload.variations[product_id], function(variation_id, qty) {
                params.payload.variations[product_id][variation_id] = 0;
            })
        }

        iwptp_cart(params);
    })

    // toggle content (show more / less)
    $('body').on('click', '.iwptp-toggle-trigger', function(e) {
        e.preventDefault();
        var $this = $(this),
            $freeze_table = $this.closest('.frzTbl').find('.frzTbl-table');

        $this.closest('.iwptp-toggle-enabled').toggleClass('iwptp-toggle');
        if ($freeze_table.length) {
            $freeze_table.freezeTable('cell_resize');
        }
    })

    // freeze table selectors for originals
    function iwptp_get_container_original_table($container) {
        return $container.find('.iwptp-table:visible').not('.frzTbl-clone-table');
    }

    // get sibling tables under freeze table
    function iwptp_get_sibling_tables($table) {
        var $freeze_table = $table.closest('.frzTbl');
        if (!$freeze_table.length) {
            return $table;
        }

        return $('.iwptp-table', $freeze_table);
    }

    // get sibling rows under freeze table
    window.iwptp_get_sibling_rows = function($row) {
        var $freeze_table = $row.closest('.frzTbl');
        if (!$freeze_table.length) {
            return $row;
        }

        var product_id = $row.attr('data-iwptp-product-id'),
            variation_id = $row.attr('data-iwptp-variation-id'),
            row_selector;

        if (variation_id) {
            row_selector = '[data-iwptp-variation-id="' + variation_id + '"].iwptp-row.iwptp-product-type-variation';
        } else {
            row_selector = '[data-iwptp-product-id="' + product_id + '"].iwptp-row:not(.iwptp-product-type-variation)';
        }

        return $(row_selector, $freeze_table);
    }

    // get original row under freeze table
    function iwptp_get_original_row($row) {
        var $sibling_rows = iwptp_get_sibling_rows($row);

        $sibling_rows.each(function() {
            var $row = $(this);
            if (!$row.closest('table').hasClass('frzTbl-clone-table')) {
                $original = $row;
                return false;
            }
        })

        return $original;
    }

    // checkbox

    // -- $cb 'change' handler -> triggers '_iwptp_checkbox_change' on $row
    $('body').on('change', '.iwptp-cart-checkbox', function(e) {
        var $this = $(this),
            $row = $this.closest('.iwptp-row');

        $row.trigger('_iwptp_checkbox_change', $this.prop('checked'));
    })

    // -- $row '_iwptp_checkbox_change' handler -> triggers 'iwptp_checkbox_change' on original $row, sets same state on sibling $cb
    $('body').on('_iwptp_checkbox_change', '.iwptp-row', function(e, state) {
        var $this = $(this),
            $original_row = iwptp_get_original_row($this),
            $table = $original_row.closest('table'),
            $sibling_rows = iwptp_get_sibling_rows($this),
            $sibling_cbs = $('.iwptp-cart-checkbox', $sibling_rows);

        // unnecessary trigger
        if (!$sibling_cbs.length || !$sibling_cbs.not(':disabled').length) {
            return;
        }

        // $row .data()-> state
        $original_row.data('iwptp_checked', state);

        // $table .data()-> checked_rows
        var $table_checked_rows = $table.data('iwptp_checked_rows') ? $table.data('iwptp_checked_rows') : $();

        if (state) {
            $table_checked_rows = $table_checked_rows.add($original_row);
        } else {
            $table_checked_rows = $table_checked_rows.not($original_row);
        }

        $table.data('iwptp_checked_rows', $table_checked_rows);

        // publish event
        $original_row.trigger('iwptp_checkbox_change', state);
    })

    // -- 'iwptp_checkbox_change' handler -> set the same state on siblings rows' $cb and data on sibling rows
    $('body').on('iwptp_checkbox_change', '.iwptp-row', function(e, state) {
        var $this = $(this),
            $sibling_rows = iwptp_get_sibling_rows($this),
            $sibling_cbs = $('.iwptp-cart-checkbox', $sibling_rows);

        $sibling_rows.data('iwptp_checked', state);
        $sibling_cbs.prop('checked', state);
    })

    // -- row html class 
    $('body').on('iwptp_checkbox_change', '.iwptp-row', function(e, state) {
        var $this = $(this),
            $original_row = iwptp_get_original_row($this),
            $sibling_rows = iwptp_get_sibling_rows($this),
            html_class = 'iwptp-row--checked';

        if ($original_row.data('iwptp_checked')) {
            $sibling_rows.addClass(html_class);
        } else {
            $sibling_rows.removeClass(html_class);
        }
    })

    // -- select variation
    $('body').on('select_variation', '.iwptp-row', function(e) {
        var $this = $(this),
            selected_variation = $this.data('iwptp_variation'),
            $cb = $this.find('.iwptp-cart-checkbox');

        // reset
        $cb.removeAttr('disabled');
        $cb.removeClass('iwptp-cart-checkbox--disabled');

        if (
            (!selected_variation ||
                $.isEmptyObject(selected_variation)
            ) ||
            (!selected_variation.is_in_stock &&
                !selected_variation.is_on_backorder
            )
        ) {
            $this.trigger('iwptp_checkbox_change', false);
            $cb.attr('disabled', true);
            $cb.addClass('iwptp-cart-checkbox--disabled');

        }
    })

    // -- qty
    // -- -- '.qty' change handler -> trigger '_iwptp_checkbox_change' on $row 
    setTimeout(function() { // too many 3rd party modules trigger $qty at page load
        $('body').on('change', '.iwptp input.qty, .iwptp select.iwptp-qty-select', function(e) {
            var $this = $(this),
                $row = $this.closest('.iwptp-row'),
                $original_row = iwptp_get_original_row($row);

            if (
                $this.closest('form.cart').length ||
                (
                    // don't permit auto-check when initial-val is min
                    $this.attr('data-iwptp-initial-value') === 'min' &&
                    !$original_row.data('iwptp_checked')
                )

            ) {
                return;
            }

            $original_row.trigger('_iwptp_checkbox_change', !!parseFloat($this.val()));
        })
    }, 1);

    // -- -- raise qty to min or reduce to initial qty upon 'iwptp_checkbox_change'
    $('body').on('iwptp_checkbox_change', '.iwptp-row', function(e, state) {
        var $this = $(this),
            $sibling_rows = iwptp_get_sibling_rows($this),
            $sibling_qty = $('input.qty, select.iwptp-qty-select', $sibling_rows),
            $sibling_qty_wrappers = $('.iwptp-quantity', $sibling_rows);

        $sibling_qty.each(function() {
            var $this = $(this),
                val = $this.val(),
                min = $this.attr('data-iwptp-min') ? parseFloat($this.attr('data-iwptp-min')) : $this.attr('min'),
                initial_qty = $this.attr('data-iwptp-initial-value');

            if (state) {
                if (!val ||
                    val === "0" ||
                    isNaN(val)
                ) {
                    val = min
                }

            } else {
                if (initial_qty == 'empty') {
                    val = '';
                } else if (initial_qty == '0') {
                    val = 0
                } else {
                    val = min;
                }

            }

            $sibling_qty.val(val);
            limit_qty_controller($sibling_qty_wrappers);

        })
    })

    // -- -- update total upon 'iwptp_checkbox_change'
    $('body').on('iwptp_checkbox_change', '.iwptp-row', function(e, state) {
        var $this = $(this),
            $sibling_rows = iwptp_get_sibling_rows($this);

        update_row_total($sibling_rows);
    })

    // -- 'iwptp_checkbox_change' handler -> create / update / hide '.iwptp-cart-checkbox-trigger' button
    $('body').on('iwptp_checkbox_change', '.iwptp-row', iwptp_checkbox_trigger_init);

    function iwptp_checkbox_trigger_init() {
        // setup $checkbox_trigger
        var $checkbox_trigger = $('.iwptp-cart-checkbox-trigger');

        if (!$checkbox_trigger.length) {
            var html = $('#tmpl-iwptp-cart-checkbox-trigger').html();
            $checkbox_trigger = $(html).appendTo('body');

        } else {
            $checkbox_trigger.removeClass('iwptp-hide');
        }

        var $checked_rows = $();

        $('.iwptp-table:visible').each(function() {
            var $this = $(this),
                $_checked_rows = $this.data('iwptp_checked_rows') && $this.data('iwptp_checked_rows').length ? $this.data('iwptp_checked_rows') : $();
            $checked_rows = $checked_rows.add($_checked_rows);
        })

        // hide
        if (!$checked_rows.length) {
            $checkbox_trigger.hide();

            return;
        }

        // show & update count
        var qty = 0;
        $checked_rows.each(function() {
            var $this = $(this),
                $qty = $('.qty, .iwptp-qty-select', $this).first(),
                val = $qty.length ? parseFloat($qty.val()) : 1;
            if (!isNaN(val)) {
                qty = ((val * 1e12) + (qty * 1e12)) / 1e12;
            }
        })

        $checkbox_trigger
            .data({
                'iwptp_checked_rows': $checked_rows,
                'iwptp_qty': qty,
            })
            .find('.iwptp-total-selected')
            .text(qty);

        $checkbox_trigger
            .trigger('iwptp_checkbox_trigger_updating')
            .show()
            .trigger('iwptp_checkbox_trigger_updated');

    }

    // -- heading
    // -- -- check all $cb in table via $heading_cb '.iwptp-cart-checkbox-heading'
    $('body').on('click', '.iwptp-cart-checkbox-heading', function() {
        var $this = $(this),
            state = $this.prop('checked'),
            $container = $this.closest('.iwptp'),
            $table = iwptp_get_container_original_table($container),
            $rows = $('.iwptp-row', $table);

        $rows.trigger('_iwptp_checkbox_change', state);
        $('iwptp-cart-checkbox--last-clicked', $table).removeClass('iwptp-cart-checkbox--last-clicked');
    })

    // -- -- auto toggle heading checkbox
    $('body').on('iwptp_checkbox_change', '.iwptp-row', function(e, state) {
        // using setTimeout, bring down multiplee calls to just one
        clearTimeout(window.iwptp_cb_heading);
        window.iwptp_cb_heading = setTimeout(function() {
            $('.iwptp').each(function() {
                var $container = $(this),
                    $heading_cb = $container.find('.iwptp-cart-checkbox-heading').filter(':visible'),
                    $cbs = $container.find('.iwptp-cart-checkbox'),
                    state = !$cbs.filter(':visible').not(':disabled').not(':checked').length;
                $heading_cb.prop('checked', state);
            })
        }, 100);
    })

    // -- shift + click
    // -- -- record shift key
    $('body').on('keydown', function(e) {
        if (e.shiftKey) {
            iwptp_shiftKey = true;
            $('body').one('keyup', function(e) {
                iwptp_shiftKey = false;
            })
        }
    })

    // -- -- act upon $cb shift key selected by user
    $('body').on('change', '.iwptp-cart-checkbox', function(e, iwptp_sync) {
        if (iwptp_sync) {
            return false;
        }

        var $this = $(this),
            $table = $this.closest('.iwptp-table'),
            $cb = $('.iwptp-cart-checkbox', $table);
        $last_clicked = $cb.filter('.iwptp-cart-checkbox--last-clicked'),
            checked = $this.prop('checked');

        $last_clicked.removeClass('iwptp-cart-checkbox--last-clicked');
        $this.addClass('iwptp-cart-checkbox--last-clicked');

        if (
            $last_clicked.length &&
            typeof iwptp_shiftKey !== 'undefined' &&
            iwptp_shiftKey
        ) {
            var min = Math.min($cb.index($this), $cb.index($last_clicked)),
                max = Math.max($cb.index($this), $cb.index($last_clicked));

            $cb
                .filter(function() {
                    var $this = $(this),
                        index = $cb.index($this);

                    if ($this.prop('disabled')) {
                        return false;
                    }

                    if (
                        index >= min &&
                        index <= max
                    ) {
                        return true;
                    } else {
                        return false;
                    }

                })
                .prop('checked', checked)
                .trigger('change', true);
        }

    })

    // -- select / clear all
    $('body').on('click', '.iwptp-add-selected__select-all, .iwptp-add-selected__clear-all', function(e) {
        var $this = $(this),
            state = !!$this.hasClass('iwptp-add-selected__select-all');
        $container = $this.closest('.iwptp'),
            $table = iwptp_get_container_original_table($container),
            $rows = $('.iwptp-row', $table);

        $rows.trigger('_iwptp_checkbox_change', state);
        $('iwptp-cart-checkbox--last-clicked', $table).removeClass('iwptp-cart-checkbox--last-clicked');
    })

    // -- -- toggle the buttons
    $('body').on('iwptp_checkbox_change', '.iwptp-row', update_table_add_selected_to_cart);

    function update_table_add_selected_to_cart() {
        var _ = this;
        setTimeout(function() {
            var $this = $(_),
                $container = $this.closest('.iwptp'),
                $table = iwptp_get_container_original_table($container),
                $checked_rows = $table.data('iwptp_checked_rows') ? $table.data('iwptp_checked_rows') : $();
            $add_checked = $('.iwptp-add-selected:visible', $container);

            $add_checked.removeClass('iwptp-add-selected--unselected iwptp-add-selected--single-item-selected');

            if ($checked_rows.length) {
                var qty = 0,
                    cost = 0;

                $checked_rows.each(function() {
                    var $this = $(this),
                        $qty = $('.qty, .iwptp-qty-select', $this).first(),
                        val = $qty.length ? parseFloat($qty.val()) : 1;
                    if (!isNaN(val)) {
                        qty = ((val * 1e12) + (qty * 1e12)) / 1e12;
                    }

                    if (!$this.data('iwptp-total')) {
                        update_row_total(iwptp_get_sibling_rows($this));
                    }

                    var product_total = $this.data('iwptp-total');

                    cost = ((cost * 1e12) + (product_total * 1e12)) / 1e12;
                })

                $('.iwptp-total-selected', $add_checked).text(qty);
                $('.iwptp-total-selected-cost .iwptp-amount', $add_checked).text(format_price_figure(cost));

                if (qty == 1) {
                    $add_checked.addClass('iwptp-add-selected--single-item-selected');
                }

            } else {
                $add_checked.addClass('iwptp-add-selected--unselected');

            }

        }, 100);
    }

    // price decimal
    function format_price_figure(price) {
        price = parseFloat(price);

        // decimal 
        if (price !== parseInt(price)) {
            price = parseFloat(price).toFixed(iwptp_params.price_decimals);
        } else {
            price = parseInt(price);
        }

        // decimal separator
        price = (price + "").replace(".", iwptp_params.price_decimal_separator);

        return price;
    }

    // follow wc settings for price display
    function format_price(num) {
        if (!num &&
            num !== '0' &&
            num !== 0
        ) {
            return '';
        }

        return iwptp_params.price_format
            .replace('%1$s', iwptp_params.currency_symbol)
            .replace('%2$s', format_price_figure(num));
    }

    // -- -- duplicate the buttons under table as well
    function duplicate_select_all($container) {
        var $add_checked = iwptp_get_container_element('.iwptp-add-selected.iwptp-duplicate-enabled:visible', $container),
            $pagination = iwptp_get_container_element('.iwptp-pagination.iwptp-device-laptop', $container);

        if (
            $add_checked.length &&
            !$pagination.prev('.iwptp-add-selected').length
        ) {
            $pagination.before(function() {
                var $clone = $add_checked.clone();
                $clone.addClass('iwptp-add-selected--footer iwptp-in-footer');
                if ($add_checked.closest('.iwptp-right').length) {
                    $clone.addClass('iwptp-laptop__text-align--right');
                }
                return $clone;
            });
        }
    }

    // -- add to cart
    $('body').on('click', '.iwptp-cart-checkbox-trigger, .iwptp-cart-checkbox-trigger--local', iwptp_cart_checkbox);

    function iwptp_cart_checkbox() {
        var $this = $(this),
            products = {},
            variations = {},
            attributes = {},
            addons = {},
            measurement = {},
            nyp = {}, // name your price
            $checked_rows = $(),
            $table = $();

        if ($this.hasClass('iwptp-cart-checkbox-trigger')) { // global
            var $container = $('.iwptp');

            $container.each(function() {
                var $this = $(this);
                $table = $table.add(iwptp_get_container_original_table($this));
            })

            $this.addClass('iwptp-hide');

        } else { // local
            var $container = $this.closest('.iwptp');
            $table = iwptp_get_container_original_table($container);

        }

        $table.each(function() {
            var $this = $(this);
            $table_checked_rows = $this.data('iwptp_checked_rows') ? $this.data('iwptp_checked_rows') : $();

            $checked_rows = $checked_rows.add($table_checked_rows);
        })

        $checked_rows.each(function() {
            var $row = $(this),
                product_id = $row.attr('data-iwptp-product-id'),
                variation_id = false,
                variation_attributes = false,
                $qty = $('.qty, .iwptp-qty-select', $row).first(),
                val = parseFloat($qty.length ? $qty.val() : 1);
            if (isNaN(val)) {
                val = 0;
            }

            if (typeof products[product_id] === 'undefined') {
                products[product_id] = val;
            } else {
                products[product_id] += val; // variation
            }

            // variable
            if ($row.hasClass('iwptp-product-type-variable')) {
                var data = $row.data();

                if (
                    data.iwptp_variation_selected &&
                    data.iwptp_variation_available &&
                    data.iwptp_complete_match &&
                    data.iwptp_variation_id
                ) {
                    variation_id = data.iwptp_variation_id;

                    if (data.iwptp_attributes) {
                        variation_attributes = data.iwptp_attributes;
                    }
                }

                // variation
            } else if ($row.hasClass('iwptp-product-type-variation')) {
                variation_id = $row.attr('data-iwptp-variation-id');
                variation_attributes = JSON.parse($row.attr('data-iwptp-variation-attributes'));

            }

            if (variation_id) {
                if (!variations[product_id]) {
                    variations[product_id] = {};
                }

                if (!variations[product_id][variation_id]) {
                    variations[product_id][variation_id] = val;
                }

                if (variation_attributes) {
                    attributes[variation_id] = variation_attributes;
                }
            }

            // addons
            if ($row.hasClass('iwptp-product-has-addons')) {
                addons[product_id] = iwptp_get_addons($row);
            }

            // measurement
            if ($row.hasClass('iwptp-product-has-measurement')) {
                measurement[product_id] = get_measurement($row);
            }

            // name your price
            if ($row.hasClass('iwptp-product-has-name-your-price')) {
                nyp[product_id] = get_nyp($row);
            }
        });

        // uncheck before iwptp_cart else $total will be reset
        $checked_rows.trigger('_iwptp_checkbox_change', false);

        var payload = {
            'products': products,
            'addons': addons,
            'mini_cart': ($('.iwptp-mini-cart').length > 0) ? $('.iwptp-mini-cart').attr('data-settings') : '',
            'measurement': measurement,
            'variations': variations,
            'attributes': attributes,
            'nyp': nyp,
        };

        setTimeout(function() {
            iwptp_cart({
                payload: payload,
                redirect: $this.attr('data-iwptp-redirect-url')
            });
        }, 150)
    }

    // addons
    window.iwptp_get_addons = function($row) {
        var $form = $('.iwptp-add-to-cart-wrapper form', iwptp_get_sibling_rows($row)),
            addons = {};

        // WooCommerce Custom Product Addons
        var $wcpa_fields = $form.find('.wcpa_form_outer');
        if ($wcpa_fields.length) {
            var $fields = $wcpa_fields,
                $_form = $('<form>');

            $_form
                .append($fields.clone())
                .find('select').each(function() { // cloned select needs correct value
                    var $this = $(this),
                        name = $this.attr('name'),
                        value = $fields.find('[name="' + name + '"]select').val();
                    $this.val(value);
                });

            $.each($_form.serializeArray(), function(i, field) {
                var field_name = field.name,
                    suffix_index = field_name.indexOf('--iwptp');

                if (-1 !== suffix_index) { // must be checkbox or radio
                    field_name = field_name.substring(0, suffix_index);

                    if (field.name.slice(-1) == ']') { // checkbox
                        if (!addons[field_name]) {
                            addons[field_name] = [];
                        }
                        addons[field_name].push(field.value);
                        return;

                    } else { // radio
                        addons[field_name] = field.value;

                    }

                } else { // other field types
                    addons[field_name] = field.value;

                }
            });

            // official WooCommerce Product Addons
        } else {
            $.each($form.serializeArray(), function(i, field) {
                var field_name = field.name;

                if (field_name.slice(-2) == '[]') {
                    field_name = field_name.substring(0, field_name.length - 2);
                }

                if (typeof addons[field_name] === 'undefined') {
                    addons[field_name] = field.value; // add prop

                } else {
                    if (typeof addons[field_name] !== 'object') { // make it an array
                        addons[field_name] = [addons[field_name]];
                    }
                    addons[field_name].push(field.value); // update prop

                }
            });

        }

        return addons;
    }

    // measurement
    function get_measurement($row) {
        var $price_calculator = $('.iwptp-add-to-cart-wrapper form #price_calculator', iwptp_get_sibling_rows($row)),
            measurement = {};

        $('input', $price_calculator).each(function() {
            var $this = $(this);
            measurement[$this.attr('name')] = $this.val();
        })

        return measurement;
    }

    // name your price
    function get_nyp($row) {
        var $nyp = get_nyp_input_element($row),
            val = 0;

        if ($nyp.length) {
            val = $nyp.val();
        }

        return val;
    }

    function get_nyp_input_element($row) {
        return $('.iwptp-name-your-price--input', iwptp_get_sibling_rows($row));
    }



    // search through filter options
    $('body').on('iwptp_after_every_load', '.iwptp', function() {
        var $this = $(this),
            $nav_dropdown = $('.iwptp-dropdown.iwptp-filter', $this);

        $nav_dropdown.each(function() {
            var $this = $(this);
            if (
                $this.hasClass('iwptp-filter--search-filter-options-enabled') &&
                !$('.iwptp-search-filter-options', $this).length
            ) {
                var $menu = $('.iwptp-dropdown-menu', $this),
                    placeholder = $this.attr('data-iwptp-search-filter-options-placeholder');

                $menu.prepend('<input type="text" class="iwptp-search-filter-options" placeholder="' + placeholder + '" />');

                $('.iwptp-search-filter-options', $menu).nextAll().wrapAll($('<div class="iwptp-search-filter-option-set" style="max-height: ' + $menu.css('max-height') + ';"></div>'));

                $menu.css('max-height', 'none');
            }
        })

    })

    $('body').on('keyup input', '.iwptp-search-filter-options', function(e) {
        var $this = $(this),
            val = $this.val().toLowerCase().trim(),
            $filter = $this.closest('.iwptp-filter'),
            $option = $('.iwptp-dropdown-option', $filter);

        e.preventDefault();

        if (!val) {
            $option.show();

            $('.iwptp-ac-open', $filter).removeClass('iwptp-ac-open');

            $('input[type=radio], input[type=checkbox]', $option).each(function() {
                var $this = $(this);
                if ($this.is(':checked')) {
                    $this.closest('.iwptp-dropdown-option.iwptp-accordion').addClass('iwptp-ac-open');
                }
            })
            return;
        }

        $option.each(function() {
            var $this = $(this),
                label = $this.text().toLowerCase().trim();
            if (label.indexOf(val) == -1) {
                $this.hide();
            } else {
                $this.show();
                $this.closest('.iwptp-dropdown-option.iwptp-accordion').addClass('iwptp-ac-open');
            }
        })
    })

    // global product search shortcode
    $('body').on('change keydown keyup', '.iwptp-global-search__keyword-input', function() {
        var $this = $(this),
            $search = $this.closest('.iwptp-global-search');

        if ($this.val()) {
            $search.removeClass('iwptp-global-search--empty');
        } else {
            $search.addClass('iwptp-global-search--empty');
        }
    })

    $('.iwptp-global-search__keyword-input').trigger('change');

    // -- facade
    $('body').on('change', '.iwptp-global-search__category-selector', function() {
        var $this = $(this),
            value = $this.val(),
            $option = $('option[value="' + value + '"]', $this),
            text = $option.text().trim(),
            $facade = $this.siblings('.iwptp-global-search__category-selector-facade');

        $('.iwptp-global-search__category-selector-facade__text', $facade).text(text);
    })

    // -- redirect empty
    $('body').on('submit', '.iwptp-global-search__form', function(e) {
        var $this = $(this),
            keyword = $this.find('.iwptp-global-search__keyword-input').val(),
            $select = $this.find('.iwptp-global-search__category-selector'),
            clear_redirect_url = $this.attr('data-iwptp-clear-redirect-url'),
            clear_redirect = $this.attr('data-iwptp-clear-redirect'),
            redirect = $this.attr('data-iwptp-redirect'),
            category = $('.iwptp-global-search__category-selector', $this).val(),
            action = $this.attr('action');

        // select 'All' category
        if (!keyword) {

            if (clear_redirect !== 'category') {
                $select.val('').change();
            }

            // redirect
            if (clear_redirect_url) {
                e.preventDefault();
                window.location = clear_redirect_url;
            }

        } else if (
            redirect == 'category' &&
            category
        ) {
            e.preventDefault();
            window.location = iwptp_product_category_links[category] + '?s=' + keyword;

        } else if (redirect == 'shop') {
            e.preventDefault();
            var url = action + '?s=' + keyword;
            if (category) {
                url += '&iwptp_search_category=' + category;
            }
            window.location = url;

        } else if (redirect == 'search') {
            e.preventDefault();
            var url = action + '?s=' + keyword + '&post_type=product';
            if (category) {
                url += '&iwptp_search_category=' + category;
            }
            window.location = url;

        }

    })

    // -- clear
    $('body').on('click', '.iwptp-global-search__clear', function() {
        var $this = $(this),
            $input = $this.siblings('.iwptp-global-search__keyword-input'),
            $form = $this.closest('.iwptp-global-search__form');
        $input.val('');
        $form.submit();
    })

    // -- focus / blur
    $('body')
        .on('focus', '.iwptp-global-search__keyword-input', function() {
            var $this = $(this),
                $wrapper = $this.parent();
            $wrapper.addClass('iwptp-global-search__keyword-input-wrapper--focus');
        })
        .on('blur', '.iwptp-global-search__keyword-input', function() {
            var $this = $(this),
                $wrapper = $this.parent();
            $wrapper.removeClass('iwptp-global-search__keyword-input-wrapper--focus');
        })

    // cart

    window.iwptp_cart = function(params) {
        if (!window.iwptp_cart_call_id) {
            window.iwptp_cart_call_id = 1;
        } else {
            ++window.iwptp_cart_call_id;
        }

        var _params = {
            payload: {
                iwptp_cart_call_id: window.iwptp_cart_call_id
            },
            before: false,
            always: false,
            redirect: false,
            external_payload: {}
        }

        params = $.extend({}, _params, params ? params : {});

        params.payload.iwptp_cart_call_id = window.iwptp_cart_call_id;
        params.payload.cart_widget_permitted = cart_widget_permitted();

        // view
        $('.iwptp-cart-widget').addClass('iwptp-cart-widget--loading'); // cart widget

        // product req data used by view
        var product_request = {
            products: params.payload.products ? $.extend({}, params.payload.products) : {},
            variations: params.payload.variations ? $.extend({}, params.payload.variations) : {}
        };

        if (typeof params.payload !== 'undefined' && params.payload.variation_form && typeof params.external_payload !== 'undefined' && params.external_payload['add-to-cart']) {
            var product_id = params.external_payload['add-to-cart'],
                variation_id = params.external_payload['variation_id'] ? params.external_payload['variation_id'] : '',
                quantity = parseFloat(params.external_payload['quantity']);

            product_request.products[product_id] = quantity;

            if (variation_id) {
                product_request.variations[product_id] = {};
                product_request.variations[product_id][variation_id] = quantity;
            }
        }

        $('.iwptp-row:visible').each(function() {
            var $row = $(this),
                $sibling_rows = iwptp_get_sibling_rows($row),
                product_id = $row.attr('data-iwptp-product-id'),
                variation_id = $row.attr('data-iwptp-variation-id'),
                $button = $(cart_button_selector, $row),
                $remove = $('.iwptp-remove', $row),
                in_cart = parseFloat($row.attr('data-iwptp-in-cart'));

            if (product_request.products) {
                $.each(product_request.products, function(id, qty) {
                    if (id == product_id) {
                        if (variation_id && product_request.variations && product_request.variations[product_id] && typeof product_request.variations[product_id][variation_id] == 'undefined') {
                            return;
                        }

                        loading_badge_on_button($button);

                        if (0 === qty && in_cart) {
                            $row.addClass('iwptp-removing-product');

                        } else {
                            $row.addClass('iwptp-adding-product');

                            var $qty = $('input.qty', $row),
                                $iwptp_qty = $qty.not('.cart .qty'),
                                initial = $iwptp_qty.attr('data-iwptp-initial-value'),
                                min = parseFloat($iwptp_qty.attr('data-iwptp-min')),
                                return_to_initial = $iwptp_qty.attr('data-iwptp-return-to-initial');

                            if (return_to_initial) {
                                $sibling_rows.trigger('_iwptp_checkbox_change', false);

                                if (initial == 'min') {
                                    $qty.val(min);

                                } else if (initial === '0') {
                                    $qty.val(0);


                                } else if (initial === 'empty') {
                                    $qty.val('');

                                }
                            }

                            var $wrapper = $qty.closest('.iwptp-quantity');
                            if ($wrapper.length) {
                                limit_qty_controller($wrapper);
                            }

                            // dropdown
                            var $qty = $('select.iwptp-qty-select', $row),
                                $first = $qty.children('option:first-child');

                            $qty.val($first.attr('value'));

                        }

                        // total
                        var $total = $('.iwptp-total', $sibling_rows),
                            _qty = variation_id ? product_request.variations[product_id][variation_id] : qty;

                        if ($total.length) {
                            update_row_total($sibling_rows, _qty);
                        }
                    }
                })
            }
        })

        var data = $.extend({}, { iwptp_payload: params.payload, lang: iwptp_i18n.lang }, params.external_payload ? params.external_payload : {});
        data.nonce = iwptp_params.ajax_nonce;
        $('body').trigger('iwptp_cart_request', data);

        if (params.payload.use_cache && window.iwptp_cart_result_cache) {
            window.iwptp_cart_result_cache.payload.iwptp_cart_call_id = window.iwptp_cart_call_id;
            $('body').trigger('iwptp_cart', window.iwptp_cart_result_cache);
        } else {
            $.post(
                iwptp_params.wc_ajax_url.replace("%%endpoint%%", "iwptp_cart"),
                data,
                function(result) {
                    window.iwptp_cart_result_cache = $.extend({}, result, { success: true, notice: '', is_cache: true });
                    $('body').trigger('iwptp_cart', result);
                }
            ).always(
                function(result) {
                    if (params.always) {
                        params.always(result);
                    }

                    if (params.redirect) {
                        if (!result.success && result.notice) {
                            $('body').on('click touchstart', '.iwptp-notice-wrapper', function() {
                                window.location = params.redirect;
                            })
                        } else { // redirect immediately
                            window.location = params.redirect;
                        }
                    }
                }
            );
        }

    }

    $('body').on('iwptp_cart', function(e, result) {
        // cart widget

        $('.iwptp-cart-widget').removeClass('iwptp-cart-widget--loading');

        if (result.cart_widget && $('.iwptp-mini-cart-items').length > 0) {
            $('.iwptp-mini-cart-items').html(result.cart_widget);
        }

        if (result.cart_subtotal && ('.iwptp-mini-cart-subtotal').length > 0) {
            $('.iwptp-mini-cart-subtotal').html(result.cart_subtotal);
        }

        if (result.cart_quantity && ('.iwptp-mini-cart-total-quantity').length > 0) {
            $('.iwptp-mini-cart-total-quantity').html(result.cart_quantity + ' ' + ((result.cart_quantity > 1) ? 'Items' : 'Item'));
        }

        iwptpCheckMiniCartVisibility();

        // if (result.payload.iwptp_cart_call_id === window.iwptp_cart_call_id) { // latest call resolved
        // }

        // error
        // if (!result.success) {
        //     var $body = $('body'),
        //         $notice = $('<div class="iwptp-notice-wrapper">' + result.notice + '</div>');

        //     $body.append($notice);
        //     $body.one('click', function () {
        //         $notice.remove();
        //     });
        // }

        // if (result.cart_widget && cart_widget_permitted()) {
        // var $body = $('body'),
        //     $old = $('.iwptp-cart-widget').not('.iwptp-cart-checkbox-trigger'),
        //     $new = $(result.cart_widget);

        // if ($new.hasClass('iwptp-hide')) {
        //     $body.removeClass('iwptp-cart-widget-visible');
        // } else {
        //     $body.addClass('iwptp-cart-widget-visible');
        // }

        // $body.append($new);
        // $old.remove();

        // if ($('.iwptp-cart-widget-container').length > 0) {
        //     $('.iwptp-cart-widget-container').html($(result.cart_widget));
        // }
        // }

        // added / removed
        var added = removed = false;

        if (result.payload && result.payload.products && result.in_cart) {
            $.each(result.payload.products, function(product_id, product_qty) {
                var variation_id = variation_qty = false;

                if (result.payload.variations && typeof result.payload.variations[product_id] !== 'undefined') {
                    variation_id = Object.keys(result.payload.variations[product_id])[0];
                    variation_qty = Object.values(result.payload.variations[product_id])[0];
                }

                if (product_qty === "0") {
                    if ($.isEmptyObject(result.in_cart) || !result.in_cart.length || !result.in_cart[product_id] || (variation_id && (!result.in_cart[product_id][variation_id]))) {
                        removed = true;
                    }
                } else {
                    if (!$.isEmptyObject(result.in_cart) && result.in_cart[product_id] && (!variation_id || result.in_cart[product_id][variation_id])) {
                        added = true;
                    }
                }
            })
        }

        if (result.payload && result.payload.variation_form) {
            added = true;
        }

        var in_cart_products = [];

        if (result.in_cart) {
            $.each(result.in_cart, function(key, val) {
                if (typeof val === 'object') { // variation
                    var qty = 0,
                        total = 0;

                    // qty
                    $.each(val, function(key2, val2) {
                        var _total = result.in_cart_total[key][key2];

                        // discreet entries for variations
                        in_cart_products.push({
                            id: key2,
                            type: 'variation',
                            quantity: val2,
                            total: _total
                        });

                        qty += parseFloat(val2);
                        total += parseFloat(_total);
                    });

                    in_cart_products.push({
                        id: key,
                        type: 'variable',
                        quantity: qty,
                        total: total
                    });

                } else {
                    in_cart_products.push({
                        id: key,
                        type: 'simple',
                        quantity: val,
                        total: result.in_cart_total[key]
                    });
                }
            })
        }

        // view
        var $rows = $('.iwptp-row');
        $rows.each(function() {
            var $row = $(this),
                type = $row.attr('data-iwptp-type');

            product_id = $row.attr('data-iwptp-product-id'),
                variation_id = $row.attr('data-iwptp-variation-id'),
                id = type == 'variation' ? $row.attr('data-iwptp-variation-id') : $row.attr('data-iwptp-product-id'),
                cart_item = false,
                min_call_id = $row.data('iwptp-min-cart-call-id');

            if (result.payload && min_call_id > result.payload.iwptp_cart_call_id) {
                return;
            }

            $.each(in_cart_products, function(key, item) {
                if ((variation_id && variation_id == item.id) || (!variation_id && product_id == item.id)) {
                    cart_item = item;
                    return false;
                }
            });

            // iwptp initiated adding / removing process is complete
            $row.removeClass('iwptp-adding-product iwptp-removing-product');

            // update 'in cart' qty
            var cart_qty = cart_item ? cart_item.quantity : 0;
            $row.attr('data-iwptp-in-cart', cart_qty);
            $in_cart = $('.iwptp-in-cart', $row).text(cart_qty);
            $in_cart.each(function() {
                var $this = $(this),
                    template = $this.attr('data-iwptp-template');

                $this.text(template.replace('{n}', cart_qty));
            })

            // update 'in cart' total
            var cart_total = cart_item ? cart_item.total : 0;
            $('.iwptp-total', $row).attr('data-iwptp-in-cart-total', cart_total);
            update_row_total($row);

            // -- enable
            if (cart_qty) {
                $in_cart.removeClass('iwptp-disabled');
                // -- disable
            } else {
                $in_cart.addClass('iwptp-disabled');
            }

            // badge

            if (!$row.hasClass('iwptp-adding-product')) {
                $button = $(cart_button_selector, $row);
                add_count_badge_to_button(cart_qty, $button);

                if (!$button.hasClass('iwptp-out-of-stock')) {
                    enable_button($button);
                }
            }

            // remove

            if (!$row.hasClass('iwptp-removing-product')) {
                var $remove = $('.iwptp-remove', $row);

                // -- enable
                if (cart_item) {
                    $remove.removeClass('iwptp-disabled');
                } else {
                    $remove.addClass('iwptp-disabled');
                    $('.add_to_cart_button', $row).removeClass('added').next('.added_to_cart').remove();
                }
            }
        })

        if (result.fragments && !result.payload.skip_cart_triggers && !result.is_cache) {
            var trigger_lock = true,
                $body = $('body'),
                $button = false;

            if (result.payload && result.payload.products && Object.keys(result.payload.products).length === 1) {
                $.each(result.payload.products, function(product_id, qty) {
                    if (qty) {
                        $button = $('<button data-product_id="' + product_id + '">');
                    } else {
                        $button = $('<a href="" data-product_id="' + product_id + '">');
                    }
                })
            }

            if (added) {
                $body.trigger('added_to_cart', [result.fragments, result.cart_hash, $button, trigger_lock]);
            }

            if (removed) {
                $body.trigger('removed_from_cart', [result.fragments, result.cart_hash, $button, trigger_lock]);
            }
        }
    }); //end


    $('body').on('added_to_cart removed_from_cart', function(e, fragment, cart_hash, $button, trigger_lock) {
        if (!trigger_lock) {
            iwptp_cart({
                payload: { skip_cart_triggers: true }
            });
        }
    });

    $(document).on('click', '.iwptp-mini-cart-item-delete', function() {
        iwptpDeleteCartItem($(this).val());
    });

    $(document).on('click', '.iwptp-mini-clear-cart-button', function() {
        iwptpCartClear();
    });

    $(document).on('click', '.iwptp-mini-cart-float-toggle-close-button-top', function() {
        $('.iwptp-mini-cart-toggle-button').trigger('click');
    });

    // table heading offset based on nav header
    $('body').on('iwptp_layout', '.iwptp', function(e, data) {
        var $this = $(this),
            $freeze_nav_sidebar = $('.iwptp-navigation.iwptp-left-sidebar.iwptp-sticky:visible', $this),
            $freeze_nav_header = $('.iwptp-navigation.iwptp-header.iwptp-sticky:visible', $this),
            $freeze_table_heading = $('.frzTbl-fixed-heading-wrapper-outer:visible', $this),
            sc_attrs = $this.data('iwptp-sc-attrs'),
            device = get_device(),
            offset = parseInt(sc_attrs[device + '_scroll_offset'] ? sc_attrs[device + '_scroll_offset'] : 0);

        $freeze_nav_sidebar.css('top', offset);

        if (device == 'laptop') {
            $freeze_nav_header.css('top', offset);

            offset += parseInt($freeze_nav_header.outerHeight());

            $freeze_table_heading.css({ top: offset });

        } else {
            offset += parseInt($freeze_nav_sidebar.outerHeight());
            $freeze_nav_header.css({ top: offset });

            offset += $freeze_nav_header.outerHeight();
            $freeze_table_heading.css({ top: offset });
        }

    });

    // dynamic filters
    function dynamic_filters_lazy_load($container) {
        // dynamic recount / hide filters not enabled
        if (!$container.data('iwptp_sc_attrs').dynamic_filters_lazy_load ||
            (!$container.data('iwptp_sc_attrs').dynamic_recount &&
                !$container.data('iwptp_sc_attrs').dynamic_hide_filters
            )
        ) {
            return;
        }

        var key = $container.attr('data-iwptp--dynamic-filters-lazy-load--key'),
            _options = $container.attr('data-iwptp--dynamic-filters-lazy-load--filter-options');

        if (!key ||
            !_options
        ) {
            return;
        }

        var options = JSON.parse(_options),
            filters = ['category', 'attribute', 'availability', 'on_sale', 'taxonomy'];

        $('.iwptp-filter', $container).each(function() {
            var $this = $(this),
                filter = $this.attr('data-iwptp-filter');

            if (-1 !== $.inArray(filter, filters)) {
                dynamic_filters_lazy_load__fetch($this, key, options);
            }
        })

    }

    function dynamic_filters_lazy_load__fetch($filter, key, options) {
        var filter = $filter.attr('data-iwptp-filter'),
            taxonomy = $filter.attr('data-iwptp-taxonomy'),
            filter_options = [],
            $container = $filter.closest('.iwptp');

        $.each(options, function(i, option) {
            if (
                filter == option['filter'] &&
                (
                    filter !== 'attribute' ||
                    taxonomy == option['taxonomy']
                )
            ) {
                filter_options.push(option);
            }
        })

        $.ajax({
            url: iwptp_params.wc_ajax_url.replace("%%endpoint%%", "iwptp__dynamic_filter__lazy_load"),
            method: 'GET',
            data: {
                nonce: iwptp_params.ajax_nonce,
                iwptp__dynamic_filter__key: key,
                iwptp__dynamic_filter__options: filter_options
            },
            success: function(result) {
                var result = JSON.parse(result),
                    $style = $('style#' + key, $container),
                    $script = $('script#' + key, $container);

                // append style and script        
                if (!$style.length) {
                    $style = $('<style id="' + key + '"></style>').prependTo($container);
                }
                $style.append($(result.style).html());

                if (!$script.length) {
                    $script = $('<script id="' + key + '"></script>').prependTo($container);
                }
                $script.append($(result.style).html());

                // add count
                $filter = $filter.add('.iwptp-nav-modal .iwptp-filter[data-iwptp-filter="' + filter + '"]');
                if (taxonomy) {
                    $filter = $filter.filter('[data-iwptp-taxonomy="' + taxonomy + '"]');
                }

                var add_count = false
                sc_attrs = $container.attr('data-iwptp-sc-attrs');

                if (sc_attrs && sc_attrs.length > 2) {
                    sc_attrs = JSON.parse(sc_attrs);

                    if (sc_attrs.dynamic_recount) {
                        add_count = true;
                    }
                }

                if (add_count) {
                    $.each(result.options, function(i, option) {
                        var count = '<span class="iwptp-count">(' + option.count + ')</span>',
                            $label = $('label[data-iwptp-value="' + option.value + '"]', $filter),
                            $icon = $label.children('.iwptp-icon'),
                            $prev_count = $('.iwptp-count', $label);

                        $prev_count.remove(); // in case duplicate taxonomy filter is printed

                        if ($icon.length) {
                            $icon.before(count);
                        } else {
                            $label.append(count);
                        }
                    })
                }

                $filter.removeClass('iwptp--dynamic-filters--loading-filter');
            }
        })

    }

    // cart widget permission
    function cart_widget_permitted() {
        var url = window.location.href.split('?')[0],
            include_match = false,
            exclude_match = false,
            include_urls = false,
            exclude_urls = false,
            site_url = iwptp_params.site_url + '/';

        if (!iwptp_params.cart_widget_enabled_site_wide &&
            !$('.iwptp').length
        ) {
            return false;
        }

        if (iwptp_params.cart_widget_include_urls.trim()) {
            include_urls = iwptp_params.cart_widget_include_urls.trim().replace(/\r\n/g, "\n").split("\n");
        }

        if (iwptp_params.cart_widget_exclude_urls.trim()) {
            exclude_urls = iwptp_params.cart_widget_exclude_urls.trim().replace(/\r\n/g, "\n").split("\n");
        }

        if (include_urls.length) {
            include_match = false;
            $.each(include_urls, function(i, path) {
                path = path.trim();

                if (path.indexOf(site_url) === 0) {
                    path = path.substring(site_url.length, path.length);
                }

                if (path.trim() == "/") {
                    if (url === site_url) {
                        include_match = true;
                    }
                    return;
                }

                if (path.trim() == "/*") {
                    include_match = true;
                    return;
                }

                path = path.replace(/(^\s*\/)|(\/\s*$)/g, ''); // ensure no slash at start or end
                if ('*' === path.substr(-1)) {
                    var remove = 1;
                    if ('/*' === path.substr(-2)) {
                        remove = 2;
                    }
                    if (-1 !== url.indexOf(site_url + path.substring(0, (path.length - remove)) + '/')) {
                        include_match = true;
                    }

                } else {
                    path += '/';
                    if (url == site_url + path) {
                        include_match = true;
                    }
                }

            })

            if (!include_match) {
                return false;
            }
        }

        if (exclude_urls.length) {
            exclude_match = false;

            $.each(exclude_urls, function(i, path) {
                path = path.trim();

                if (path.indexOf(site_url) === 0) {
                    path = path.substring(site_url.length, path.length);
                }

                if (path.trim() == "/") {
                    if (url === site_url) {
                        exclude_match = true;
                    }
                    return;
                }

                if (path.trim() == "/*") {
                    exclude_match = true;
                    return;
                }

                path = path.replace(/(^\s*\/)|(\/\s*$)/g, ''); // ensure no slash at start or end
                if ('*' === path.substr(-1)) {
                    var remove = 1;
                    if ('/*' === path.substr(-2)) {
                        remove = 2;
                    }

                    if (-1 !== url.indexOf(site_url + path.substring(0, (path.length - remove)) + '/')) {
                        exclude_match = true;
                    }

                } else {
                    path += '/';
                    if (url == site_url + path) {
                        exclude_match = true;
                    }
                }

            })

            if (exclude_match) {
                return false;
            }
        }

        return true;
    }

    // multirange

    // -- input[type=number] change
    $('body').on('input change', '.iwptp-range-input-min, .iwptp-range-input-max', function() {
        var $this = $(this),
            $container = $this.closest('.iwptp-range-options-main'),

            $min = $container.find('.iwptp-range-input-min'),
            min_val = parseFloat($min.val()),

            $max = $container.find('.iwptp-range-input-max'),
            max_val = parseFloat($max.val())

        $range = $container.find('.iwptp-range-slider.original');

        if (!$range.length) {
            return;
        }

        // ensure max stays greater than min
        if (min_val > max_val) {
            if ($this.hasClass('iwptp-range-input-min')) {
                $max.val(min_val);
            } else {
                $min.val(max_val);
            }
        }

        // max step fix 
        var actual_max = parseFloat($this.attr('data-iwptp-actual-max')),
            val = parseFloat($this.val());

        if (!isNaN(actual_max) &&
            !isNaN(val) &&
            val > actual_max
        ) {
            $this.val(actual_max);
        }

        $range.val($min.val() + ',' + $max.val());
    })

    // -- input[type=range] change
    $('body').on('input change', '.iwptp-range-slider', function() {
        var $this = $(this),
            $container = $this.closest('.iwptp-range-options-main'),

            $min = $('.iwptp-range-input-min', $container),
            $max = $('.iwptp-range-input-max', $container),

            $range_original = $container.find('.iwptp-range-slider.original'),
            $range_ghost = $container.find('.iwptp-range-slider.ghost'),

            min = parseFloat($range_original.prop('valueLow')),
            max = parseFloat($range_original.prop('valueHigh')),

            permitted_max = parseFloat($this.attr('max')),
            step = parseFloat($this.attr('step'));

        // range input max fix
        if (max + step > permitted_max) {
            max = permitted_max; // sending this to input[type="number"]
        }

        $min.val(min);
        $max.val(max);
    })

    // freeze table select variation radio fix
    $('body').on('before_freeze_table_build', '.iwptp-table', function() {
        var $this = $(this), // original table
            $radio_set = $('.iwptp-select-variation-radio-multiple-wrapper', $this);

        $radio_set.each(function() {
            var $this = $(this),
                $radio = $('input[type="radio"]', $this),
                $checked = $radio.filter(':checked');

            $radio.not($checked).removeAttr('checked', '');
            $checked.attr('checked', 'checked');
        })
    })
    $('body').on('after_freeze_table_build', '.iwptp-table', function() {
        var $this = $(this), // original table
            $sibling_tables = iwptp_get_sibling_tables($this),
            $radio_set = $('.iwptp-select-variation-radio-multiple-wrapper', $sibling_tables),
            $container = $this.closest('.iwptp');

        $radio_set.each(function() {
            var $this = $(this),
                $radio = $('input[type="radio"]', $this),
                $checked = $radio.filter('[checked="checked"]'),
                name = $radio.attr('name');

            $radio.attr('name', name + Math.floor(Math.random() * 1e10));
            $checked.prop('checked', true);
        })

        // variation data
        $('.iwptp-row', $this).each(function() {
            var $original_row = $(this),
                $sibling_rows = iwptp_get_sibling_rows($original_row),
                props = [
                    'iwptp_variation',
                    'iwptp_variation_id',
                    'iwptp_complete_match',
                    'iwptp_attributes',
                    'iwptp_variation_found',
                    'iwptp_variation_selected',
                    'iwptp_variation_available',
                    'iwptp_variation_qty'
                ];

            if ($sibling_rows) {
                $.each(props, function(i, name) {
                    $sibling_rows.data(name, $original_row.data(name));
                });
            }

        })
    })

    // variation switch
    $('body').on('select_variation', '.iwptp-product-type-variable', function(e, data) {
        var $row = get_product_rows($(this)),
            $items = $('.iwptp-variation-description__item', $row);

        $items.hide();

        if ($row.data('iwptp_variation_selected')) {
            var variation_id = $row.data('iwptp_variation_id');
            $items.filter('[data-iwptp-variation-id=' + variation_id + ']').show();
        }
    })

    // name your price
    $('body').on('select_variation', '.iwptp-product-type-variable', function(e, data) {
        var $row = get_product_rows($(this)),
            $input = $('.iwptp-name-your-price--input', $row),
            initial_value_field = $input.attr('data-iwptp-nyp-initial-value-field'),
            input = false,
            min = false,
            max = false,
            suggested = false;

        // if price has no variable switch, always stays "$min - $max" then just replace it completely with "from $_min"
        // if it has variable switch 
        // then hide it whenever selected variation has nyp


        $row.removeClass('iwptp-product-has-name-your-price');

        if ($row.data('iwptp_variation_selected')) { // variation selected
            var variation = $row.data('iwptp_variation');

            if (variation.is_nyp) {
                input = true;
                min = variation.minimum_price;
                max = variation.maximum_price;
                suggested = variation.suggested_price;

                $row.addClass('iwptp-product-has-name-your-price');
            }

        } else { // variation not selected
            input = false;

        }

        // no nyp input field
        if (!input) {
            $input.parent().addClass('iwptp-hide');
            $row.find('iwptp-price.iwptp-variable-switch').removeClass('iwptp-hide--name-your-price');

            // found nyp input field
        } else {
            $input.parent().removeClass('iwptp-hide');

            if (min) {
                $input.attr({
                    min: min,
                    'data-iwptp-nyp-minimum-price': min,
                });
            } else {
                $input.attr({
                    min: 0,
                    'data-iwptp-nyp-minimum-price': 0,
                });
            }

            if (max) {
                $input.attr('max', max);

                $input.attr({
                    max: max,
                    'data-iwptp-nyp-maximum-price': max,
                });

            } else {
                $input.attr({
                    max: '',
                    'data-iwptp-nyp-maximum-price': '',
                });
            }

            if (
                initial_value_field &&
                !$input.val()
            ) {
                var value = variation[initial_value_field + '_price'] ? variation[initial_value_field + '_price'] : '';
                $input.val(value);
            }

            $row.find('iwptp-price.iwptp-variable-switch').addClass('iwptp-hide--name-your-price');
        }

        if (!min) {
            $row.find('.iwptp-name-your-price--minimum').addClass('iwptp-hide');
        } else {
            $row.find('.iwptp-name-your-price--minimum').removeClass('iwptp-hide');
            $row.find('.iwptp-name-your-price--minimum .iwptp-amount').text(min);
        }

        if (!max) {
            $row.find('.iwptp-name-your-price--maximum').addClass('iwptp-hide');
        } else {
            $row.find('.iwptp-name-your-price--maximum').removeClass('iwptp-hide');
            $row.find('.iwptp-name-your-price--maximum .iwptp-amount').text(max);
        }

        if (!suggested) {
            $row.find('.iwptp-name-your-price--suggested').addClass('iwptp-hide');
        } else {
            $row.find('.iwptp-name-your-price--suggested').removeClass('iwptp-hide');
            $row.find('.iwptp-name-your-price--suggested .iwptp-amount').text(suggested);
        }

        $input.change();

    });

    // -- checkbox
    $('body').on('iwptp_checkbox_change', '.iwptp-row', function(e, checked) {
        var $this = $(this),
            $rows = iwptp_get_sibling_rows($this.closest('.iwptp-row')),
            $nyp = get_nyp_input_element($rows);

        if (checked) {
            nyp_validate($nyp);
        } else if (!$nyp.val()) {
            nyp_hide_error($nyp);
        }

    })

    // -- validate
    function nyp_validate($nyp) {
        var $nyp_wrapper = $nyp.parent(),
            nyp_val = $nyp.val(),
            nyp_min = $nyp.attr('data-iwptp-nyp-minimum-price'),
            nyp_max = $nyp.attr('data-iwptp-nyp-maximum-price'),
            message_template = '',
            $error = '',
            $row = $nyp.closest('.iwptp-row'),
            checked = $row.data('iwptp_checked');

        if (
            (
                nyp_val &&
                nyp_min &&
                parseFloat(nyp_val) < parseFloat(nyp_min)
            ) ||
            (
                checked &&
                !nyp_val &&
                nyp_min
            )
        ) {
            $nyp_wrapper.addClass('iwptp-name-your-price-wrapper--input-error iwptp-name-your-price-wrapper--input-error--min-price');

            $error = $('.iwptp-name-your-price-input-error-message--min-price', $nyp_wrapper);
            message_template = $error.attr('data-iwptp-error-message-template');
            $error.text(message_template.replace('[min]', format_price_figure(nyp_min)));

        } else if (
            nyp_val &&
            nyp_max &&
            parseFloat(nyp_val) > parseFloat(nyp_max)
        ) {
            $nyp_wrapper.addClass('iwptp-name-your-price-wrapper--input-error iwptp-name-your-price-wrapper--input-error--max-price');

            $error = $('.iwptp-name-your-price-input-error-message--max-price', $nyp_wrapper);
            message_template = $error.attr('data-iwptp-error-message-template');
            $error.text(message_template.replace('[max]', format_price_figure(nyp_max)));

        } else {
            $nyp_wrapper.removeClass('iwptp-name-your-price-wrapper--input-error iwptp-name-your-price-wrapper--input-error--min-price iwptp-name-your-price-wrapper--input-error--max-price');

        }
    }

    // -- hide validation errors
    function nyp_hide_error($nyp) {
        $nyp.parent().removeClass('iwptp-name-your-price-wrapper--input-error iwptp-name-your-price-wrapper--input-error--min-price iwptp-name-your-price-wrapper--input-error--max-price');
    }

    // freeze table layout checks
    var ft_check__count = 4;

    function ft_check__go() {
        ft_check__maybe_cell_resize();

        --ft_check__count;

        if (!ft_check__count) {
            return;

        } else if (ft_check__count == 3) {
            setTimeout(ft_check__go, 1);

        } else {
            setTimeout(ft_check__go, 5000);

        }

    }

    function ft_check__maybe_cell_resize($tables) {
        if (!$tables) {
            $tables = $('.iwptp-table:not(.frzTbl-clone-table)');
        }

        $tables.each(function() {
            var $this = $(this),
                freezeTable = $this.data('freezeTable');

            if (!$this.is(':visible')) {
                return;
            }

            if (!freezeTable) {
                $this.trigger('iwptp_layout', { source: 'ft_check' });

            } else if (
                freezeTable.env &&
                freezeTable.env.tableHeight
            ) {
                if ($this.height() !== freezeTable.env.tableHeight) {
                    $this.freezeTable('cell_resize');
                }
            }

        })
    }

    // // check all FT every 5 seconds for 15 sec after page load
    // ft_check__go();

    // check FT on table 1 sec after first FT layout on it
    $('body').on('after_freeze_table_build', '.iwptp-table', function() {
        var $this = $(this);
        setTimeout(function() {
            ft_check__maybe_cell_resize($this);
        }, 1000);
    })

    // }

    // trigger nav feedback from range slider and min max 
    $('body').on('change', '.iwptp-range-options-main input', function() {
        var $this = $(this),
            $filter = $this.closest('.iwptp-filter');

        $filter.find('input[type=radio]').prop('checked', false);
        nav_filter_feedback($this.closest('.iwptp-navigation'));
    })

    // refresh / block table
    // -- lock
    $('body').on('iwptp_cart_request', function(e, data) {
            if (
                data.iwptp_payload.skip_cart_triggers ||
                data.iwptp_payload.use_cache ||
                data.is_cache
            ) {
                return;
            }

            $('.iwptp').each(function() {
                var $container = $(this),
                    sc_attrs = JSON.parse($container.attr('data-iwptp-sc-attrs'));

                if (sc_attrs.refresh_table) {
                    $container.addClass('iwptp-loading iwptp-refreshing');

                } else if (sc_attrs.block_table) {
                    $container.addClass('iwptp-loading');

                }
            })
        })
        // -- refresh
    $('body').on('iwptp_cart', function(e, result) {
        if (result.payload &&
            (result.payload.skip_cart_triggers ||
                result.payload.use_cache ||
                result.is_cache)
        ) {
            return;
        }

        $('.iwptp').each(function() {
            var $container = $(this),
                sc_attrs = JSON.parse($container.attr('data-iwptp-sc-attrs')),
                table_id = $container.attr('data-iwptp-table-id');

            if (sc_attrs.refresh_table) {
                $.each(window.iwptp_cache.data, function(key, val) {
                    if (
                        key.indexOf('?' + table_id + '_') !== -1 ||
                        key.indexOf('&' + table_id + '_') !== -1
                    ) {
                        delete window.iwptp_cache.data[key];
                    }
                })

                attempt_ajax($container, '', true, 'refresh_table');

            } else if (sc_attrs.block_table) {
                $container.removeClass('iwptp-loading');

            }
        })
    })

    // berocket
    $(document).on('berocket_ajax_filtering_end', function(e) {
        $('.iwptp').each(function() {
            after_every_load($(this));
        });
    })

    // waveplayer

    //-- avoid losing functionality on cloned players during freeze table
    $('body').on('iwptp_layout', '.iwptp', function() {
        var $this = $(this);

        if (typeof WavePlayer !== 'undefined') {
            WavePlayer.loadInstances();

            if (!$iwptp_waveplayer_preserve.length) {
                return;
            }

            var $replace = get_matching_waveplayer_elm($this);

            if ($replace.length) {
                $replace.replaceWith($iwptp_waveplayer_preserve);
                $iwptp_waveplayer_preserve.find('.wvpl-waveform canvas').each(function() {
                    var $this = $(this);
                    $this.width($this.attr('data-iwptp-last-width'));
                    $this.height($this.attr('data-iwptp-last-height'));
                })

                waveplayer_active_ui_feedback.call($iwptp_waveplayer_preserve.get(0));

                $iwptp_waveplayer_preserve = $();

                WavePlayer.redrawAllInstances();
            }
        }
    })

    // -- check for and return waveplayer element in container that needs to be replaced
    function get_matching_waveplayer_elm($container) {
        if (
            WavePlayer.persistentTrack &&
            WavePlayer.instances.length
        ) {
            var instance = false,
                track_index = false;

            $.each(WavePlayer.instances, function(_instance_index, _instance) {
                var $_instances = $(_instance.node);
                if (
                    $_instances.is(':visible') &&
                    $_instances.parent().hasClass('iwptp-waveplayer-container')
                ) {
                    var $row = $_instances.closest('.iwptp-row');

                    if ($row.length) {
                        $.each(_instance.tracks, function(_track_index, _track) {
                            if (
                                _track.file === WavePlayer.persistentTrack.file &&
                                _track.product_id === WavePlayer.persistentTrack.product_id
                            ) {
                                instance = _instance;
                                track_index = _track_index;
                            }
                        })

                    }
                }
            })

            if (instance) {
                return $(instance.node).parent('.iwptp-waveplayer-container');
            }
        }

        return $();
    }

    // -- preseve active waveplayer $elm before disposing off $container
    window.$iwptp_waveplayer_preserve = $();
    $('body').on('iwptp_ajax_success', function(e, data) {
        if (typeof WavePlayer === 'undefined') {
            return;
        }

        // check if matching player in current container    
        $player = get_matching_waveplayer_elm(data.$container);

        if ($player.length) {
            $player.find('.wvpl-waveform canvas').each(function() {
                var $this = $(this);
                $this.attr({
                    'data-iwptp-last-width': $this.width(),
                    'data-iwptp-last-height': $this.height(),
                })
            })

            window.$iwptp_waveplayer_preserve = $player.detach();
        }
    })

    // -- add to cart
    $('body').on('added_to_cart', function(e, fragment, cart_hash, $button) {
        if (
            typeof WavePlayer === 'undefined' ||
            !$button
        ) {
            return;
        }

        var product_id = $button.attr('data-product_id');
        if (typeof WavePlayer.updateTrackCartStatus !== 'undefined') {
            WavePlayer.updateTrackCartStatus(product_id, 'add');
        }

        $('.wvpl-cart[data-product_id=' + product_id + ']')
            .attr('title', WavePlayer.__('Add to cart', 'waveplayer'))
            .attr('data-event', 'goToCart')
            .attr('data-callback', 'goToCart')
            .removeClass('wvpl-add_to_cart')
            .addClass('wvpl-in_cart')
            .removeClass('wvpl-spin');
    })

    // -- row color
    $('body').on('click', '.iwptp-row .iwptp-waveplayer-container', waveplayer_active_ui_feedback);

    // -- active feedback
    function waveplayer_active_ui_feedback() {
        var $this = $(this),
            active_row_background_color = $this.attr('data-iwptp-waveplayer-active-row-background-color'),
            active_row_outline_color = $this.attr('data-iwptp-waveplayer-active-row-outline-color') ? $this.attr('data-iwptp-waveplayer-active-row-outline-color') : '#4198de',
            active_row_outline_width = $this.attr('data-iwptp-waveplayer-active-row-outline-width') ? parseFloat($this.attr('data-iwptp-waveplayer-active-row-outline-width')) + 'px' : '1px',
            $rows = iwptp_get_sibling_rows($this.closest('.iwptp-row'));

        $rows
            .addClass('iwptp-waveplayer-active')
            .css({
                background: active_row_background_color ? active_row_background_color : '',
                outline: active_row_outline_width + " solid " + active_row_outline_color
            });

        $('.iwptp-row').not($rows)
            .removeClass('iwptp-waveplayer-active')
            .css({
                background: '',
                outline: ''
            });
    }


    // 3rd party tab and accordion compatibility 

    // -- elementor accordion, tab & toggle
    $('body').on('click', '.elementor-tab-title', function() {
        var $this = $(this),
            $container = $this.closest('.elementor-widget-container'),
            $iwptp = $container.find('.iwptp');

        $iwptp.trigger('iwptp_layout', { source: 'elementor__tab' });
    })

    // -- divi
    // -- -- tab
    $('ul.et_pb_tabs_controls > li').on('click', function() {
            var $this = $(this),
                index = $this.index(),
                $controls = $this.closest('.et_pb_tabs_controls'),
                $tabs = $controls.siblings('.et_pb_all_tabs'),
                $container = $tabs.children().eq(index),
                $iwptp = $container.find('.iwptp');

            setTimeout(function() {
                $iwptp.trigger('iwptp_layout', { source: 'divi__tab' });
            }, 700);
        })
        // -- -- accordion
    $('.et_pb_toggle_title').on('click', function() {
        var $this = $(this),
            $content = $this.next('.et_pb_toggle_content'),
            $iwptp = $content.find('.iwptp');

        setTimeout(function() {
            $iwptp.trigger('iwptp_layout', { source: 'divi__tab' });
        }, 700);
    })

    // -- beaver builder
    $('body').on('click', '.fl-accordion-button, .fl-tabs-label', function() {
        var $this = $(this),
            $container = $this.closest('.fl-accordion-item, .fl-tabs-panel'),
            $iwptp = $container.find('.iwptp');

        $iwptp.trigger('iwptp_layout', { source: 'beaver_builder__tab' });
    })

    // -- shortcode ultimate
    // -- -- tab
    $('body').on('click', '.su-tabs-nav span', function() {
            var $this = $(this),
                index = $this.index(),
                $nav = $this.closest('.su-tabs-nav'),
                $panes = $nav.siblings('.su-tabs-panes'),
                $container = $panes.children().eq(index),
                $iwptp = $container.find('.iwptp');

            $iwptp.trigger('iwptp_layout', { source: 'shortcode_ultimate__tab' });
        })
        // -- -- accordion
    $('body').on('click', '.su-spoiler-title', function() {
        var $this = $(this),
            $container = $this.closest('.su-spoiler'),
            $iwptp = $container.find('.iwptp');

        $iwptp.trigger('iwptp_layout', { source: 'shortcode_ultimate__accordion' });
    })

    // -- wp bakery visual composer
    // -- -- tab
    $('body').on('click', '.vce-classic-tabs-tab', function() {
            var $this = $(this),
                index = $this.index(),
                $nav = $this.closest('.vce-classic-tabs-container'),
                $panes = $nav.siblings('.vce-classic-tabs-panels-container'),
                $container = $panes.children('.vce-classic-tabs-panels').children().eq(index),
                $iwptp = $container.find('.iwptp');

            setTimeout(function() {
                $iwptp.trigger('iwptp_layout', { source: 'visual_composer__tab' });
            }, 1);
        })
        // -- -- accordion (1)
    $('body').on('click', '.vce-classic-accordion-panel-heading', function() {
            var $this = $(this),
                $container = $this.closest('.vce-classic-accordion-panel'),
                $iwptp = $container.find('.iwptp');
            setTimeout(function() {
                $iwptp.trigger('iwptp_layout', { source: 'visual_composer__accordion' });
            }, 1);
        })
        // -- -- accordion (2)
    $('body').on('click', '.vc_tta-panel-heading', function() {
        var $this = $(this),
            $container = $this.closest('.vc_tta-panel'),
            $iwptp = $container.find('.iwptp');
        setTimeout(function() {
            $iwptp.trigger('iwptp_layout', { source: 'visual_composer__accordion' });
        }, 1);
    })

    // -- responsive accordion and collapse
    $('body').on('click', '.wpsm_panel-heading', function() {
        var $this = $(this),
            $container = $this.closest('.wpsm_panel'),
            $content = $container.find('.wpsm_panel-collapse');

        $('.iwptp', $content).trigger('iwptp_layout', { source: 'rac__accordion' });
    })

    // -- king composer
    $('body').on('click', '.kc_accordion_header', function() {
        var $this = $(this),
            $container = $this.closest('.kc_accordion_section'),
            $content = $container.find('.kc_accordion_content');

        $('.iwptp', $content).trigger('iwptp_layout', { source: 'king_composer__accordion' });
    })

    // -- helpie faq
    $('body').on('mouseup touchend', '.accordion__header', function() {
        var $this = $(this),
            $container = $this.closest('.accordion__item'),
            $content = $container.find('.accordion__body');

        clearTimeout(window.iwptp_helpie_timeout);
        window.iwptp_helpie_timeout = setTimeout(function() {
            $('.iwptp:visible', $content).trigger('iwptp_layout', { source: 'helpie_faq__accordion' });
        }, 200);
    })

    // -- tabs (wpshopmart)
    $('body').on('click', '.wpsm_nav li', function() {
        var $this = $(this),
            index = $this.index(),
            $nav = $this.closest('.wpsm_nav'),
            $panes = $nav.siblings('.tab-content'),
            $container = $panes.children().eq(index),
            $iwptp = $container.find('.iwptp');

        $iwptp.trigger('iwptp_layout', { source: 'tabs_wpshopmart__tab' });
    })

    // -- tabby responsive tabs
    $('body').on('click', '.tabtitle', function() {
        var $this = $(this),
            $panel = $this.siblings('.tabcontent');

        $('.iwptp', $panel).trigger('iwptp_layout', { source: 'tabby_tab' });
    })

    $('body').on('click', '.responsive-tabs__list__item', function() {
        var $this = $(this),
            index = $this.index(),
            $container = $this.closest('.responsive-tabs'),
            $panel = $('.tabcontent', $container).eq(index);

        $('.iwptp', $panel).trigger('iwptp_layout', { source: 'tabby_tab' });
    })

    // sonaar 
    // -- audio player play / pause
    $('body').on('click', '.iwptp-player--sonaar', function() {
        var $this = $(this),
            playlist_id = $this.attr('data-iwptp-sonaar-playlist-id');

        if (
            typeof IRON == 'undefined' ||
            !playlist_id
        ) {
            return;
        }

        $('.iwptp-player--sonaar').not($this).removeClass('iwptp-player--playing');

        // pause
        if (
            IRON.sonaar.player.isPlaying &&
            IRON.sonaar.player.playlistID == playlist_id
        ) {
            IRON.sonaar.player.pause();
            $this.removeClass('iwptp-player--playing iwptp-media-loaded');

        } else {
            $this.addClass('iwptp-player--playing iwptp-media-loaded');

            IRON.sonaar.player.setPlayer({
                id: playlist_id,
                autoplay: true,
                soundwave: true
            })
        }

    })

    // -- auto set play / pause status after every load
    function sonaar_player_auto_status() {
        if (typeof IRON == 'undefined') {
            return;
        }

        var playlist_id = IRON.sonaar.player.playlistID;

        if (playlist_id) {
            $('.iwptp-player--sonaar[data-iwptp-sonaar-playlist-id="' + playlist_id + '"]').addClass('iwptp-player--playing iwptp-media-loaded');
        }

    }

    // TI Wishlist integration

    if (typeof iwptp_ti_wishlist_ids !== 'undefined') {

        // update counter widget
        setTimeout(function() {
            $('.wishlist_products_counter_number').text(iwptp_ti_wishlist_ids.length);
            if (iwptp_ti_wishlist_ids.length) {
                $('.wishlist_products_counter').addClass('wishlist-counter-with-products');
            } else {
                $('.wishlist_products_counter').removeClass('wishlist-counter-with-products');
            }
        }, 100);

        // wishlist button
        $('body').on('click', '.iwptp-wishlist', function(e) {
            var $this = $(this),
                $row = $this.closest('.iwptp-row'),
                $iwptp = $this.closest('.iwptp'),
                product_type = $row.attr('data-iwptp-type'),
                product_id = $row.attr('data-iwptp-product-id'),
                variation_id = $row.attr('data-iwptp-variation-id') ? $row.attr('data-iwptp-variation-id') : $row.data('iwptp_variation_id') ? $row.data('iwptp_variation_id') : false,
                variable_permitted = $this.attr('data-iwptp-variable-permitted');

            // variable product must have variation selected
            if (
                product_type == 'variable' &&
                !variable_permitted &&
                !variation_id
            ) {
                alert("Please select some options first");
                return;
            }

            var list_id = variation_id ? variation_id : product_id;

            $this.addClass('iwptp-loading');

            var data = {
                nonce: iwptp_params.ajax_nonce,
                tinv_wishlist_id: false,
                tinv_wishlist_name: false,
                product_type: $row.attr('data-iwptp-type'),
                product_id: $row.attr('data-iwptp-product-id'),
                product_variation: variation_id,
                redirect: false
            }

            if (variation_id) {
                var attributes = data.product_type === 'variable' ? $row.data('iwptp_attributes') : JSON.parse($row.attr('data-iwptp-variation-attributes'));

                $.extend(data, {
                    form: $.extend({
                        quantity: 1,
                        product_id: product_id,
                        variation_id: variation_id
                    }, attributes)
                });
            }

            // add
            if (-1 === iwptp_ti_wishlist_ids.indexOf(list_id)) {
                $.ajax({
                    url: iwptp_params.ajax_url,
                    method: 'POST',
                    beforeSend: function() {
                        // variation added from variable product, refresh page to show it          
                        if (
                            product_type === 'variable' &&
                            $iwptp.data('iwptp_sc_attrs').ti_wishlist
                        ) {
                            $iwptp.addClass('iwptp-loading');
                        }
                    },
                    data: $.extend({}, { product_action: 'addto' }, data),
                }).done(function(response) {
                    // variation added from variable product, refresh page to show it
                    if (
                        product_type === 'variable' &&
                        $iwptp.data('iwptp_sc_attrs').ti_wishlist
                    ) {
                        window.location.reload();
                    }

                    iwptp_ti_wishlist_update_row_view($row);

                    iwptp_ti_wishlist_growler({
                        'name': $this.attr('data-iwptp-product-name'),
                        'view_wishlist_label': $this.attr('data-iwptp-view-wishlist-label'),
                        'item_added_label': $this.attr('data-iwptp-item-added-label'),
                        'icon': $this.attr('data-iwptp-icon'),
                        'url': $this.attr('data-iwptp-custom-url'),
                        'duration_seconds': $this.attr('data-iwptp-duration-seconds'),
                    })

                    // update counter widget
                    if (
                        response &&
                        typeof response.wishlists_data !== 'undefined'
                    ) {
                        $('.wishlist_products_counter_number').text(response.wishlists_data.counter);
                        if (response.wishlists_data.counter) {
                            $('.wishlist_products_counter').addClass('wishlist-counter-with-products');
                        } else {
                            $('.wishlist_products_counter').removeClass('wishlist-counter-with-products');
                        }
                    }
                })

                // add to maintained list as well
                iwptp_ti_wishlist_ids.push(list_id);

                // update UI
                $this.addClass('iwptp-active');

                // remove
            } else {
                $.ajax({
                    url: iwptp_params.ajax_url,
                    method: 'POST',
                    data: $.extend({}, { product_action: 'remove' }, data),
                }).done(function(response) {
                    // update counter widget          
                    if (
                        response &&
                        typeof response.wishlists_data !== 'undefined'
                    ) {
                        $('.wishlist_products_counter_number').text(response.wishlists_data.counter);
                        if (response.wishlists_data.counter) {
                            $('.wishlist_products_counter').addClass('wishlist-counter-with-products');
                        } else {
                            $('.wishlist_products_counter').removeClass('wishlist-counter-with-products');
                        }
                    }
                })

                // remove from maintained list as well          
                var index = iwptp_ti_wishlist_ids.indexOf(list_id);
                if (index > -1) {
                    iwptp_ti_wishlist_ids.splice(index, 1);
                }

                iwptp_ti_wishlist_update_row_view($row);

                // update UI
                // $this.removeClass('iwptp-active');

                // remove product row from wishlist table
                if (
                    $iwptp.data('iwptp_sc_attrs').ti_wishlist
                ) {

                    var $remove_row = $row;

                    // don't remove variable product if it was being used to remove a variation only
                    if (variation_id) {
                        $remove_row = $iwptp.find('.iwptp-product-type-variation[data-iwptp-variation-id="' + variation_id + '"]');
                    }

                    $remove_row.addClass('iwptp-wishlist-removing-row');
                    setTimeout(function() {
                        $remove_row.remove();
                    }, 500);

                }
            }

        })

        function iwptp_ti_wishlist_growler(data) {
            var template = $('#iwptp-ti-wishlist-growler-template').html(),
                $growler = $(template.replace('{n}', '"' + data.name + '"')),
                reveal_class = "iwptp-ti-wishlist-growler--revealed",
                duration_ms = data.duration_seconds * 1000;

            $growler.attr('data-iwptp-icon', data.icon);
            $('.iwptp-ti-wishlist-growler').remove(); // remove previous

            if (data.item_added_label) {
                $('.iwptp-ti-wishlist-growler__label--item-added', $growler).text(data.item_added_label.replace('{n}', '"' + data.name + '"'));
            }

            if (data.view_wishlist_label) {
                $('.iwptp-ti-wishlist-growler__label--view-wishlist', $growler).text(data.view_wishlist_label.replace('{n}', '"' + data.name + '"'));
            }

            if (data.url) {
                $growler.attr('href', data.url);
            } else if (iwptp_ti_wishlist_url) {
                $growler.attr('href', iwptp_ti_wishlist_url);
            }

            $('body').append($growler);

            setTimeout(function() {
                $growler.addClass(reveal_class);
            }, 100)

            setTimeout(function() {
                $growler.removeClass(reveal_class);
            }, duration_ms);

            setTimeout(function() {
                $growler.remove();
            }, duration_ms + 500);

        }

        $('body').on('iwptp_after_every_load', '.iwptp', function() {
            var $this = $(this),
                $row = $('.iwptp-row', $this).filter(":visible");

            // variable products in wishlist were definitely added without variation selected
            if (
                $this.data('iwptp_sc_attrs') &&
                $this.data('iwptp_sc_attrs').ti_wishlist
            ) {
                $row
                    .filter('.iwptp-product-type-variable')
                    .trigger('select_variation', {
                        variation_id: false,
                        complete_match: false,
                        attributes: false,
                        variation: false,
                        variation_found: false,
                        variation_selected: false,
                        variation_available: false,
                    });
            }

            $row.each(function() {
                var $this = $(this);
                iwptp_ti_wishlist_update_row_view($this);
            })
        })

        $('body').on('select_variation', '.iwptp-row', function() {
            var $this = $(this);
            iwptp_ti_wishlist_update_row_view($this);
        })

        function iwptp_ti_wishlist_update_row_view($row) {
            var product_type = $row.attr('data-iwptp-type'),
                product_id = $row.attr('data-iwptp-product-id'),
                variation_id = $row.data('iwptp_variation_id') ? $row.data('iwptp_variation_id') : $row.attr('data-iwptp-variation-id') ? $row.attr('data-iwptp-variation-id') : false,
                $buttons = $('.iwptp-wishlist', iwptp_get_sibling_rows($row)),
                variable_permitted = $buttons.attr('data-iwptp-variable-permitted');

            if (
                product_type === 'variable' &&
                !variable_permitted &&
                !variation_id
            ) {
                $buttons
                    .removeClass('iwptp-active')
                    .addClass('iwptp-disabled');

                return;
            }

            $buttons.removeClass('iwptp-disabled iwptp-loading');

            var list_id = variation_id ? variation_id : product_id;

            if (-1 === iwptp_ti_wishlist_ids.indexOf(list_id + "")) {
                $buttons.removeClass('iwptp-active');

            } else {
                $buttons.addClass('iwptp-active');

            }
        }

    }

    // WooCommerce Wholesale Prices

    // -- select_variation handler to switch vals in iwptp_wholesale
    $('body').on('select_variation', '.iwptp-row', function() {
        var $this = $(this),
            variation = $this.data('iwptp_variation'),
            $wholesale_shortcode = $('.iwptp-wholesale', $this);

        $wholesale_shortcode.each(function() {
            var $this = $(this);

            // set the view: default or variation
            $this.removeClass('iwptp-wholesale--variation-view-enabled iwptp-wholesale--default-view-enabled');

            if (variation) {
                $this.addClass('iwptp-wholesale--variation-view-enabled');
            } else {
                $this.addClass('iwptp-wholesale--default-view-enabled');
            }

            // wholesale table
            if ($this.hasClass('iwptp-wholesale--wholesale-table')) {
                var variation_html = '-';

                if (
                    variation &&
                    variation.wholesale_price
                ) {
                    var table_html_match = variation.price_html.match(/(<table.+table)>/s);
                    if (table_html_match) {
                        variation_html = table_html_match[0];
                    }
                }

                $this.html(variation_html);
            }

            // wholesale price
            if ($this.hasClass('iwptp-wholesale--wholesale-price')) {
                var variation_text = '-';

                if (variation) {
                    var wholesale_price = '';
                    if (variation.wholesale_price) {
                        wholesale_price = variation.wholesale_price;
                    }
                    variation_text = format_price(wholesale_price);
                }

                $('.iwptp-wholesale__variation-view', $this).text(variation_text);
            }

            // original price
            if ($this.hasClass('iwptp-wholesale--original-price')) {
                var variation_text = '-';

                if (variation) {
                    var original_price = '';
                    if (variation.wholesale_price) {
                        original_price = variation.original_display_price; // cannot use display_price here because it is set to wholesale_price by IWPTPL
                    } else {
                        original_price = variation.display_price;
                    }
                    variation_text = format_price(original_price);
                }

                $('.iwptp-wholesale__variation-view', $this).text(variation_text);
            }

            // wholesale label        
            if ($this.hasClass('iwptp-wholesale--wholesale-label')) {
                $this.removeClass('iwptp-wholesale--variation-is-on-wholesale-view-enabled iwptp-wholesale--variation-is-not-on-wholesale-view-enabled');
                if (variation) {
                    if (variation.wholesale_price) {
                        $this.addClass('iwptp-wholesale--variation-is-on-wholesale-view-enabled');
                    } else {
                        $this.addClass('iwptp-wholesale--variation-is-not-on-wholesale-view-enabled');
                    }

                }
            }

        })

    })

    // Variation Swatches
    $('body').on('change', '.iwptp-row .variations_form .variation_id', function() {
        var $this = $(this),
            $form = $this.closest('.variations_form');

        if ($form.hasClass('wvs-loaded')) {
            get_select_variation_from_cart_form($form);
        }
    })

    // WC Request a Quote (by Addify)

    if (typeof iwptp_afrfq_params == 'undefined') {
        iwptp_afrfq_params = {
            product_ids: [],
            view_quote_url: '',
            view_quote_label: '',
        };
    }

    // -- button status & switch html class
    $('body').on('iwptp_after_every_load', '.iwptp', function() {
        var $iwptp = $(this);

        // switch html class
        $('.afrfqbt, .afrfqbt_single_page', $iwptp).removeClass('afrfqbt afrfqbt_single_page').addClass('iwptp-afrfqbt'); // override with IWPTPL handler

        // remove afrfqbt success message
        $('.added_quote_pro').remove();

        // set added status
        afrfqbt_status($iwptp);
    })

    setTimeout(afrfqbt_status, 500); // some external script removes 'added' html class  

    function afrfqbt_status($container) { // $container will be $('.iwptp-afrfqbt'), $row, $iwptp or $body
        if (typeof $container === 'undefined') {
            $container = $('body');
        }

        var $raq_buttons = $container.is('.iwptp-afrfqbt') ? $container : $('.iwptp-afrfqbt', $container);

        $raq_buttons.each(function() {
            var $raq_button = $(this),
                $row = $raq_button.closest('.iwptp-row'),
                id = false,
                product_type = $row.attr('data-iwptp-type'),
                $prev_view_link = $raq_button.siblings('.iwptp-afrfqbt-view-quote-wrapper');

            if (product_type === 'variable') {
                id = $row.data('iwptp_variation_id');

            } else if (product_type === 'variation') {
                id = $row.attr('data-iwptp-variation-id');

            } else {
                id = $row.attr('data-iwptp-product-id');

            }

            if (-1 !== $.inArray(parseInt(id), iwptp_afrfq_params.product_ids)) {
                $raq_button.addClass('added');

                if (!$prev_view_link.length) {
                    $raq_button.after('<div class="iwptp-afrfqbt-view-quote-wrapper"><a href="' + iwptp_afrfq_params.view_quote_url + '" class="iwptp-afrfqbt-view-quote">' + iwptp_afrfq_params.view_quote_label + '</a></div>');
                }

            } else {
                $raq_button.removeClass('added');
                $prev_view_link.remove();

            }
        })
    }

    // -- ev: 'select_variation' - enable / disable button
    $('body').on('select_variation', '.iwptp-product-type-variable', function() {
        var $this = $(this),
            variation_id = $this.data('iwptp_variation_id'),
            $raq_button = $('.iwptp-afrfqbt', $this);

        afrfqbt_status($this);

        if (variation_id) { // enable button      
            $raq_button.removeClass('disabled');

        } else { // disable button
            $raq_button.addClass('disabled');

        }
    })

    // -- AJAX req

    $('body').on('click', '.iwptp-afrfqbt', function(e) {
        e.preventDefault();

        var $this = $(this),
            $row = $this.closest('.iwptp-row'),
            product_id = $row.attr('data-iwptp-product-id'),
            variation_id = false,
            variation_attributes = false,
            qty = 0;

        if ($('.cart .qty', $row).length) {
            qty = $('.cart .qty', $row).val();
        } else {
            qty = $('.qty', $row).val();
        }

        if (!qty) {
            qty = 1;
        }

        if ($row.hasClass('iwptp-product-type-variable')) {
            variation_id = $row.data('iwptp_variation_id');
            variation_attributes = $row.data('iwptp_attributes');

        } else if ($row.hasClass('iwptp-product-type-variation')) {
            variation_id = $row.attr('data-iwptp-variation-id');
            variation_attributes = $row.data('iwptpVariationAttributes');
        }

        if (
            $row.hasClass('iwptp-product-type-variable') &&
            !variation_id
        ) {
            alert(iwptp_i18n.i18n_make_a_selection_text)
            return;
        }

        if ($this.hasClass('disabled')) {
            return;
        }

        $this.removeClass('added'); // avoid conflict with .loading animation 

        if (variation_id) {
            var ajax_data = {
                action: 'add_to_quote_single_vari',
                form_data: {
                    product_id: product_id,
                    variation_id: variation_id,
                    'add-to-cart': product_id,
                    quantity: qty,
                },
                nonce: afrfq_phpvars.nonce
            };

            ajax_data.form_data = $.param(ajax_data.form_data);

            $.extend(ajax_data.form_data, variation_attributes);

        } else {
            var ajax_data = {
                action: 'add_to_quote_single',
                product_id: product_id,
                quantity: qty,
                woo_addons: false,
                woo_addons1: false,
                nonce: afrfq_phpvars.nonce
            };
        }

        $.ajax({
                url: afrfq_phpvars.admin_url,
                method: 'POST',
                beforeSend: function() {
                    $this.addClass('loading');
                },
                data: ajax_data
            })
            .done(function(response) {
                // keep list of products in raq
                // upon after every load, refer to this list
                $this.removeClass('loading');

                if (variation_id) {
                    iwptp_afrfq_params.product_ids.push(parseInt(variation_id));
                } else {
                    iwptp_afrfq_params.product_ids.push(parseInt(product_id));
                }

                afrfqbt_status($this);

                if (response !== 'success') { // menu mini quote
                    $('.quote-li').replaceWith(response['mini-quote']);
                }

            })
    })

    // init tables
    if ($('.iwptp, .iwptp-lazy-load').length) {
        $('.iwptp').each(function() {
            after_every_load($(this));
        });
    }

    // init cart widget

    var cart_init_required = false;

    if (
        document.cookie.indexOf('woocommerce_items_in_cart') !== -1 ||
        (
            typeof iwptp_cart_result_cache !== 'undefined' &&
            iwptp_cart_result_cache.cart_quantity
        )
    ) {
        cart_init_required = true;
    }

    if (cart_init_required) {
        iwptp_cart({
            payload: { skip_cart_triggers: true }
        });
    }

    $(window).on('pageshow', function(e) {
        if (e.originalEvent.persisted) {
            iwptp_cart({
                payload: { skip_cart_triggers: true }
            });
        }
    });

    $(document).on('click', '.iwptp-left-sidebar-toggle', function() {
        $(this).closest('.iwptp-left-sidebar').toggleClass('iwptp-left-sidebar-opened');
    });

    if ($('.iwptp-cart-popup').length > 0 && $('.iwptp-mini-cart-float-toggle-arrow-bottom svg').length > 0) {
        $('.iwptp-mini-cart-float-toggle-arrow-bottom svg path').css({
            fill: $('.iwptp-cart-popup').css('background-color')
        });
    }

    iwptpCheckMiniCartVisibility();

    // iwptp_update_cart_items(iwptp_params.cart.cart_contents);
})



// module: toggle child row
jQuery(function($) {

    // -- register parent in child row data
    function store_parent_in_child_row_data() {
        var $this = $(this);
        $('.iwptp-child-row', $this).each(function() {
            var $this = $(this);
            $this.data('iwptp_parent_row', $this.prev());
        })
    }
    $('.iwptp').each(store_parent_in_child_row_data);
    $('body').on('iwptp_after_every_load', '.iwptp', store_parent_in_child_row_data)

    // -- click ev handler
    $('body').on('click', '.iwptp-child-row-toggle', function() {
        var $this = $(this),
            $row = $this.closest('.iwptp-row'),
            $iwptp = $this.closest('.iwptp');

        $this.toggleClass('iwptp-child-row-toggle--closed');

        if ($this.is('td')) {
            $row.next('.iwptp-child-row').toggle();

            // heading status
            if ($('td.iwptp-child-row-toggle:not(.iwptp-child-row-toggle--closed)', $iwptp).length) {
                $('th.iwptp-child-row-toggle.iwptp-child-row-toggle--closed', $iwptp).removeClass('iwptp-child-row-toggle--closed');
            } else {
                $('th.iwptp-child-row-toggle', $iwptp).addClass('iwptp-child-row-toggle--closed');
            }
        } else { // th
            if ($this.hasClass('iwptp-child-row-toggle--closed')) {
                $('.iwptp-child-row-toggle', $iwptp).addClass('iwptp-child-row-toggle--closed');
                $('.iwptp-child-row', $iwptp).hide();
            } else {
                $('.iwptp-child-row-toggle', $iwptp).removeClass('iwptp-child-row-toggle--closed');
                $('.iwptp-child-row', $iwptp).show();
            }
        }

        // row class
        $('td.iwptp-child-row-toggle', $iwptp).each(function() {
            var $this = $(this),
                $row = $this.closest('.iwptp-row');

            if ($this.hasClass('iwptp-child-row-toggle--closed')) {
                $row.removeClass('iwptp-has-child-row--visible');
            } else {
                $row.addClass('iwptp-has-child-row--visible');
            }
        })

        var freeze_table = $('.iwptp-table.frzTbl-table', $iwptp).data('freezeTable')
        if (freeze_table) {
            freeze_table.cell_resize();
        }

    })

    // -- click anywhere in row
    $('body').on('click', '.iwptp-has-child-row--click-anywhere', function(e) {
        var $this = $(this),
            $row = $this.closest('.iwptp-row');
        if (!$(e.target).closest('.iwptp-child-row-toggle, a, .iwptp-tooltip, input, button, .iwptp-button, .iwptp-link, .iwptp-quantity').length) {
            $('.iwptp-child-row-toggle', $row).click();
        }
    })

    // // reload on device change
    // $(window).on('iwptp_device_change', function(){
    //   window.location.reload();
    // })

})


// module: instant search 
jQuery(function($) {

    // -- add the html class, stop 'enter' key handler
    function init_instant_search() {
        var $this = $(this),
            sc_attrs = $this.data('iwptp_sc_attrs');

        if (sc_attrs.instant_search) {
            $('.iwptp-search-wrapper', $this).addClass('iwptp-instant-search');
            $('.iwptp-instant-search .iwptp-search-input', $this).on('keydown', function(e) {
                if (e.keyCode === 13 || e.which === 13) {
                    e.stopPropagation();
                }
            })
        }
    }

    $('.iwptp').each(init_instant_search);
    $('body').on('iwptp_after_every_load', '.iwptp', init_instant_search);

    // -- search logic
    $('body').on('keyup input', '.iwptp-instant-search .iwptp-search-input', function(e) {
        var $this = $(this),
            val = $this.val().toLowerCase().trim(),
            $iwptp = $this.closest('.iwptp'),
            $table = $('.iwptp-table:visible:not(.frzTbl-clone-table)', $iwptp),
            $row = $('.iwptp-row', $table);

        if (!val) {
            $row.removeClass('iwptp-row--instant-search-hidden');

        } else {
            $row.each(function() {
                var $this = $(this),
                    match = false;

                // using indexed value
                var text = $this.data('iwptp_instant_search_text');
                if (!text) {
                    text = $this.text().toLowerCase().trim();
                    $this.data('iwptp_instant_search_text', text);
                }

                if (text.indexOf(val) !== -1) {
                    match = true;
                }

                if (match) {
                    $this.removeClass('iwptp-row--instant-search-hidden');
                } else {
                    $this.addClass('iwptp-row--instant-search-hidden');
                }

                // child row -- reveal both if either has match
                if ($this.hasClass('iwptp-child-row')) {
                    $parent_row = $this.data('iwptp_parent_row');

                    if (!$this.hasClass('iwptp-row--instant-search-hidden') ||
                        !$parent_row.hasClass('iwptp-row--instant-search-hidden')
                    ) {
                        $this.add($parent_row).removeClass('iwptp-row--instant-search-hidden');
                    }
                }
            })

        }

        // iwptp_assign_even_odd_row_classes( $table );

        // freeze table
        var $original_table = $('.iwptp-table.frzTbl-table', $iwptp),
            $ft_clone_table = $('.iwptp-table.frzTbl-clone-table', $iwptp),
            $ft_clone_row = $('.iwptp-row', $ft_clone_table);

        $ft_clone_row.each(function() {
            var $this = $(this),
                id = $this.attr('data-iwptp-product-id'),
                $original_row = $('.iwptp-row:not(.iwptp-child-row)[data-iwptp-product-id="' + id + '"]', $original_table),
                html_class__hide = 'iwptp-row--instant-search-hidden';

            if ($original_row.hasClass(html_class__hide)) {
                $this.addClass(html_class__hide)
            } else {
                $this.removeClass(html_class__hide)
            }
        })

        if ($original_table.length) {
            var $row = $('.iwptp-row', $ft_clone_table);

            setTimeout(function() {
                $original_table.data('freezeTable').cell_resize();
            }, 300)
        }

    })

})

// module: instant sort
jQuery(function($) {
    // -- init
    $('.iwptp').each(init_instant_sort);
    $('body').on('iwptp_after_every_load', '.iwptp', init_instant_sort);

    function init_instant_sort() {
        var $this = $(this),
            sc_attrs = $this.data('iwptp_sc_attrs');

        if (sc_attrs.instant_sort) {
            var sort_data = [];

            $('.iwptp-row:not(.iwptp-child-row)', $this).each(function() {
                var $this = $(this),
                    id = $this.attr('data-iwptp-product-id'),
                    variation_id = $this.attr('data-iwptp-variation-id'),
                    product_sort_data = $.extend({}, {
                            id: id,
                            variation_id: variation_id
                        },
                        JSON.parse($this.attr('data-iwptp-instant-sort-props'))
                    );
                sort_data.push(product_sort_data);
            })

            $this.data('iwptp_sort_data', sort_data);

            // column heading sort icons handler
            $this.off('click.iwptp', '.iwptp-heading.iwptp-sortable');
            $this.on('click.iwptp', '.iwptp-heading.iwptp-sortable', function() {
                var $this = $(this),
                    $sorting_icons = $('.iwptp-sorting-icons', $this),
                    new_order = $sorting_icons.hasClass('iwptp-sorting-asc') ? 'desc' : 'asc',
                    $iwptp = $this.closest('.iwptp'),
                    all_sort_params = JSON.parse($iwptp.attr('data-iwptp-instant-sort-params')),
                    current_sort_params = {};

                $.each(all_sort_params.column_heading, function(id, params) {
                    if ($sorting_icons.hasClass('iwptp-' + id)) {
                        current_sort_params = params;
                        return false;
                    }
                })

                $.extend(current_sort_params, { order: new_order })

                iwptp_instant_sort(current_sort_params, $iwptp);

                instant_sort_ui_feedback(current_sort_params, $iwptp);
            });

            // 'Sort By' dropdown handler 
            $this.on('change', '[data-iwptp-filter="sort_by"] input[type="radio"]', function(e) {
                e.stopPropagation();

                var $this = $(this),
                    $option = $this.closest('.iwptp-option, .iwptp-dropdown-option'), // @TODO include dropdown option
                    $iwptp = $this.closest('.iwptp'),
                    all_sort_params = JSON.parse($iwptp.attr('data-iwptp-instant-sort-params')),
                    index = $option.index(),
                    current_sort_params = all_sort_params.dropdown[index];

                iwptp_instant_sort(current_sort_params, $iwptp);

                instant_sort_ui_feedback(current_sort_params, $iwptp);
            })

        }
    }

    function instant_sort_ui_feedback(current_sort_params, $iwptp) {
        var all_sort_params = JSON.parse($iwptp.attr('data-iwptp-instant-sort-params'));

        if (-1 !== $.inArray(current_sort_params.orderby, ['rating', 'price-desc'])) {
            current_sort_params.order = 'desc';
        }

        if (current_sort_params.orderby == 'price-desc') {
            current_sort_params.orderby = 'price';
        }

        // 'Sort By' dropdown (or row)
        $.each(all_sort_params['dropdown'], function(dropdown_option_index, option_sort_params) {
            if (-1 !== $.inArray(option_sort_params.orderby, ['rating', 'price-desc'])) {
                option_sort_params.order = 'desc';
            }

            if (option_sort_params.orderby == 'price-desc') {
                option_sort_params.orderby = 'price';
            }

            if (
                current_sort_params.orderby !== option_sort_params.orderby ||
                (
                    current_sort_params.order &&
                    current_sort_params.order.toLowerCase() !== option_sort_params.order.toLowerCase()
                ) ||
                (
                    current_sort_params.orderby == 'meta_key' &&
                    current_sort_params.meta_key &&
                    current_sort_params.meta_key.toLowerCase() !== option_sort_params.meta_key.toLowerCase()
                ) ||
                (-1 !== $.inArray(current_sort_params.orderby, ['attribute', 'attribute_num']) &&
                    current_sort_params.orderby_attribute &&
                    current_sort_params.orderby_attribute.toLowerCase() !== option_sort_params.orderby_attribute.toLowerCase()
                ) ||
                (
                    current_sort_params.orderby == 'taxonomy' &&
                    current_sort_params.orderby_taxonomy &&
                    current_sort_params.orderby_taxonomy.toLowerCase() !== option_sort_params.orderby_taxonomy.toLowerCase()
                )
            ) {
                return;
            }

            var $dropdown = $('[data-iwptp-filter="sort_by"]', $iwptp), // might be row
                $selected_input = $dropdown.find('input').eq(dropdown_option_index),
                $selected_option = $selected_input.closest('.iwptp-dropdown-option, .iwptp-option'),
                $heading_label = $('.iwptp-dropdown-label', $dropdown);

            $heading_label.text($('span', $selected_option).text());
            $selected_input.prop('checked', true);
            $selected_option.addClass('iwptp-active').siblings().removeClass('iwptp-active');

            $dropdown.removeClass('iwptp-open');

        })

        // 'Sorting' column heading icons
        $.each(all_sort_params['column_heading'], function(id, option_sort_params) {

            if (
                current_sort_params.orderby !== option_sort_params.orderby ||
                (
                    current_sort_params.orderby == 'meta_key' &&
                    current_sort_params.meta_key &&
                    current_sort_params.meta_key.toLowerCase() !== option_sort_params.meta_key.toLowerCase()
                ) ||
                (-1 !== $.inArray(current_sort_params.orderby, ['attribute', 'attribute_num']) &&
                    current_sort_params.orderby_attribute &&
                    current_sort_params.orderby_attribute.toLowerCase() !== option_sort_params.orderby_attribute.toLowerCase()
                ) ||
                (
                    current_sort_params.orderby == 'taxonomy' &&
                    current_sort_params.orderby_taxonomy &&
                    current_sort_params.orderby_taxonomy.toLowerCase() !== option_sort_params.orderby_taxonomy.toLowerCase()
                )
            ) {
                return;
            }

            var $sorting_icons = $('.iwptp-' + id, $iwptp),
                new_order = current_sort_params.order.toLowerCase();

            // UI feedback
            $('.iwptp-sorting-icons', $iwptp).removeClass('iwptp-sorting-asc iwptp-sorting-desc');
            $('.iwptp-sorting-icon', $iwptp).removeClass('iwptp-active iwptp-inactive');

            if (new_order == 'asc') {
                $sorting_icons
                    .addClass('iwptp-sorting-asc')
                    .removeClass('iwptp-sorting-desc');

            } else {
                $sorting_icons
                    .addClass('iwptp-sorting-desc')
                    .removeClass('iwptp-sorting-asc');
            }

            $('.iwptp-sorting-' + new_order + '-icon', $sorting_icons).addClass('iwptp-active');
        })

    }

    window.iwptp_instant_sort = function(params, $iwptp) {
        var sort_data = $iwptp.data('iwptp_sort_data');

        if (!params.order) {
            params.order = 'asc';
        }

        params.order = params.order.toLowerCase();

        if (-1 !== $.inArray(params.orderby, ['price-desc', 'rating'])) {
            params.order = 'desc';
        }

        switch (params.orderby) {
            case 'title':
                sort_data.sort(function(a, b) {
                    return params.order == 'asc' ? a.title.localeCompare(b.title) : b.title.localeCompare(a.title);
                })

                break;

            case 'sku': // as text
                sort_data.sort(function(a, b) {
                    return params.order == 'asc' ? a.sku.localeCompare(b.sku) : b.sku.localeCompare(a.sku);
                })

                break;

            case 'sku_num':
                sort_data.sort(function(a, b) {
                    var a_sku_num = isNaN(parseFloat(a.sku)) ? 0 : parseFloat(a.sku),
                        b_sku_num = isNaN(parseFloat(b.sku)) ? 0 : parseFloat(b.sku);

                    return params.order == 'asc' ? a_sku_num - b_sku_num : b_sku_num - a_sku_num;
                })

                break;

            case 'menu_order':
                sort_data.sort(function(a, b) {
                    return params.order == 'asc' ? a.menu_order - b.menu_order : b.menu_order - a.menu_order;
                })

                break;

            case 'price':
            case 'price-desc':
                sort_data.sort(function(a, b) {
                    var a_price = a.price,
                        b_price = b.price;

                    if (
                        params.order == 'asc' &&
                        a.min_price
                    ) {
                        a_price = a.min_price;
                    }

                    if (
                        params.order == 'desc' &&
                        a.max_price
                    ) {
                        a_price = a.max_price;
                    }

                    if (
                        params.order == 'asc' &&
                        b.min_price
                    ) {
                        b_price = b.min_price;
                    }

                    if (
                        params.order == 'desc' &&
                        b.max_price
                    ) {
                        b_price = b.max_price;
                    }

                    return params.order == 'asc' ? a_price - b_price : b_price - a_price;
                })

                break;

            case 'meta_value': // as text
                sort_data.sort(function(a, b) {
                    var a_meta = a['meta_value__' + params.meta_key],
                        b_meta = b['meta_value__' + params.meta_key];

                    return params.order == 'asc' ? a_meta.localeCompare(b_meta) : b_meta.localeCompare(a_meta);
                })

                break;

            case 'meta_value_num':
                sort_data.sort(function(a, b) {

                    var a_meta = a['meta_value__' + params.meta_key],
                        b_meta = b['meta_value__' + params.meta_key];

                    var a_meta_num = isNaN(parseFloat(a_meta)) ? 0 : parseFloat(a_meta),
                        b_meta_num = isNaN(parseFloat(b_meta)) ? 0 : parseFloat(b_meta);

                    return params.order == 'asc' ? a_meta_num - b_meta_num : b_meta_num - a_meta_num;
                })

                break;

            case 'attribute': // as text
                sort_data.sort(function(a, b) {
                    var a_val = a['attribute__' + params.orderby_attribute],
                        b_val = b['attribute__' + params.orderby_attribute];

                    return params.order == 'asc' ? a_val.localeCompare(b_val) : b_val.localeCompare(a_val);
                })

                break;

            case 'attribute_num': // as number
                sort_data.sort(function(a, b) {

                    var a_val = a['attribute__' + params.orderby_attribute],
                        b_val = b['attribute__' + params.orderby_attribute];

                    var a_val_num = isNaN(parseFloat(a_val)) ? 0 : parseFloat(a_val),
                        b_val_num = isNaN(parseFloat(b_val)) ? 0 : parseFloat(b_val);

                    return params.order == 'asc' ? a_val_num - b_val_num : b_val_num - a_val_num;
                })

                break;

            case 'taxonomy': // as text
                sort_data.sort(function(a, b) {
                    var a_val = a['taxonomy__' + params.orderby_taxonomy],
                        b_val = b['taxonomy__' + params.orderby_taxonomy];

                    return params.order == 'asc' ? a_val.localeCompare(b_val) : b_val.localeCompare(a_val);
                })

                break;

            case 'taxonomy_num': // as number
                sort_data.sort(function(a, b) {

                    var a_val = a['taxonomy__' + params.orderby_taxonomy],
                        b_val = b['taxonomy__' + params.orderby_taxonomy];

                    var a_val_num = isNaN(parseFloat(a_val)) ? 0 : parseFloat(a_val),
                        b_val_num = isNaN(parseFloat(b_val)) ? 0 : parseFloat(b_val);

                    return params.order == 'asc' ? a_val_num - b_val_num : b_val_num - a_val_num;
                })

                break;

            case 'category':
                sort_data.sort(function(a, b) {
                    return params.order == 'asc' ? a.category.localeCompare(b.category) : b.category.localeCompare(a.category);
                })

            case 'date':
            case 'popularity':
            case 'id':
            case 'rating':
                sort_data.sort(function(a, b) {
                    return params.order == 'asc' ? a[params.orderby] - b[params.orderby] : b[params.orderby] - a[params.orderby];
                })

                break;

            default:
                break;
        }

        // render
        $.each(sort_data, function(index, product_sort_data) {
            var $row = false;
            if (product_sort_data.variation_id) { // variation
                $row = $iwptp.find('.iwptp-row[data-iwptp-variation-id="' + product_sort_data.variation_id + '"]');
            } else { // other
                $row = $iwptp.find('.iwptp-row[data-iwptp-product-id="' + product_sort_data.id + '"]');
            }

            $row.each(function() {
                var $this = $(this),
                    $tbody = $this.closest('tbody');
                $this.detach().appendTo($tbody);
            })
        })

        iwptp_assign_even_odd_row_classes($('.iwptp-table', $iwptp));

    }

    function iwptp_assign_even_odd_row_classes($table) {
        var $rows = $('.iwptp-row:not(.iwptp-child-row):visible', $table),
            $child_rows = $('.iwptp-child-row', $table);

        $rows.each(function() {
            var $this = $(this);

            if ($rows.index($this) % 2) {
                $this.addClass('iwptp-even').removeClass('iwptp-odd');
            } else {
                $this.addClass('iwptp-odd').removeClass('iwptp-even');
            }
        })

        $child_rows.each(function() {
            var $this = $(this),
                $parent_row = $this.data('iwptp_parent_row');

            if ($parent_row.hasClass('iwptp-even')) {
                $this.removeClass('iwptp-odd').addClass('iwptp-even');
            } else { // odd
                $this.removeClass('iwptp-even').addClass('iwptp-odd');
            }
        })
    }

})

// module: download csv
jQuery(function($) {
    $('body').on('click', '.iwptp-csv-download', function() {
        var $this = $(this),
            session_key = $this.attr('data-iwptp-csv-session-key'),
            include_all_products = $this.attr('data-iwptp-csv-include-all-products'),
            headings = window[$this.attr('data-iwptp-headings-js-var-name')].join(','),
            file_name = $this.attr('data-iwptp-file-name');

        if ($this.hasClass('iwptp-disabled')) {
            return;
        }

        $.ajax({
            url: iwptp_params.wc_ajax_url.replace("%%endpoint%%", "iwptp_get_csv"),
            method: 'POST',
            beforeSend: function() {
                $this.addClass('iwptp-disabled iwptp-loading');
            },
            data: {
                nonce: iwptp_params.ajax_nonce,
                iwptp_csv_session_key: session_key,
                iwptp_csv_include_all_products: include_all_products
            }
        }).done(function(json_data) {
            $this.removeClass('iwptp-disabled iwptp-loading');
            build_csv_and_download(json_data, headings, file_name);
        })

    })

    function build_csv_and_download(json_data, headings, file_name) {
        var csv = headings + '\n';
        $.each(json_data, function(key, product) {
            $.each(product, function(prop, val) {
                csv += val + ",";
            })
            csv = csv.slice(0, -1) + "\n";
        })

        var $pseudo_link = $('<a>', {
            href: 'data:Application/octet-stream,' + encodeURIComponent(csv),
            download: file_name + '.csv',
        });

        $pseudo_link
            .appendTo('body')
            .get(0).click();
        $pseudo_link.remove();
    }
})

function iwptpDeleteCartItem(key) {
    jQuery.ajax({
        method: 'post',
        dataType: 'json',
        url: iwptp_params.ajax_url,
        data: {
            action: 'iwptp_delete_cart_item',
            nonce: IWPTP_DATA.ajax_nonce,
            item_key: key,
        },
        success: function(response) {
            if (response.success === true) {
                jQuery('.iwptp-mini-cart-item[data-key="' + key + '"]').remove();
                if (jQuery('.iwptp-mini-cart-item').length < 1) {
                    jQuery('.iwptp-mini-cart-items').html('Your cart is currently empty');
                }
                jQuery('.iwptp-mini-cart-subtotal').html(response.subtotal);
                jQuery('.iwptp-mini-cart-total-quantity').html(response.total_quantity + ' ' + ((response.total_quantity > 1) ? 'Items' : 'Item'));

                iwptp_update_cart_items(response.cart);
                swal("Success", "", "success");
            } else {
                swal("Error!", "", "warning");
            }
        },
        error: function(e) {
            swal("Error!", "", "warning");
        },
    })
}

function iwptpCartClear() {
    jQuery.ajax({
        method: 'post',
        dataType: 'json',
        url: iwptp_params.ajax_url,
        data: {
            action: 'iwptp_cart_clear',
            nonce: IWPTP_DATA.ajax_nonce,
        },
        success: function(response) {
            if (response.success === true) {
                jQuery('.iwptp-mini-cart-items').html('Your cart is currently empty');
                jQuery('.iwptp-mini-cart-subtotal').html(response.subtotal);
                jQuery('.iwptp-mini-cart-total-quantity').html('0 Item');
                iwptp_update_cart_items(response.cart);
            } else {}
        },
        error: function(e) {},
    })
}

function iwptpCheckMiniCartVisibility() {
    let mini_cart_settings = jQuery('.iwptp-mini-cart').attr('data-settings');
    if (mini_cart_settings && mini_cart_settings != '') {
        let settings = JSON.parse(mini_cart_settings);
        if (settings.hasOwnProperty('hide_on_zero') && settings['hide_on_zero'] == 'enable' && jQuery('.iwptp-mini-cart .iwptp-mini-cart-total-quantity').text() == '0 Item') {
            jQuery('.iwptp-mini-cart .iwptp-mini-cart-close-button').trigger('click');
            jQuery('.iwptp-mini-cart .iwptp-mini-cart-toggle-button').trigger('click');
            jQuery('.iwptp-mini-cart').addClass('iwptp-hide');
            return;
        } else {
            jQuery('.iwptp-mini-cart').removeClass('iwptp-hide');
        }
    } else {
        jQuery('.iwptp-mini-cart').removeClass('iwptp-hide');
    }
}