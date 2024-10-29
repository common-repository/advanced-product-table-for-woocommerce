if (typeof iwptp === "undefined") {
    var iwptp = {
        controller: {},
        data: {},
    };
}

jQuery(function($) {
    var controller = iwptp.controller,
        data = iwptp.data;

    /* handler functions */

    // update table title/name
    controller.update_table_title = function() {
        var $this = $(this),
            new_title = $this.val();
        $('.iwptp-editor-save-table [name="title"]').val(new_title);
    }

    // switch editor tabs
    controller.switch_editor_tabs = function() {
        var $this = $(this),
            tab = $this.attr('data-iwptp-tab'),
            $labels = $this.siblings('.iwptp-tab-label'),
            $contents = $this.siblings('.iwptp-tab-content'),
            $target_content = $contents.filter('[data-iwptp-tab=' + tab + ']'),
            active_class = 'active';

        $labels.removeClass(active_class);
        $this.addClass(active_class);

        $contents.removeClass(active_class);
        $target_content.addClass(active_class);

        window.location.hash = tab;
    }

    // toggle sub categories
    controller.toggle_sub_categories = function() {
        var $this = $(this);
        $this.parent().toggleClass('iwptp-show-sub-categories');
    }

    // auto select on click
    controller.auto_select_on_click = function() {
        var node = this;
        if (document.body.createTextRange) {
            const range = document.body.createTextRange();
            range.moveToElementText(node);
            range.select();
        } else if (window.getSelection) {
            const selection = window.getSelection();
            const range = document.createRange();
            range.selectNodeContents(node);
            selection.removeAllRanges();
            selection.addRange(range);
        }
    }

    // checklist
    //-- saved
    $('body').on('iwptp_save', function() {
            $('[data-iwptp-ck="saved"]').addClass('iwptp-done');
        })
        //-- query_selected
    $('body').on('change', '.iwptp-editor > [iwptp-model-key="query"]', function() {
            if (
                (typeof iwptp.data.query.category !== 'undefined' && iwptp.data.query.category.length) ||
                iwptp.data.query.ids ||
                iwptp.data.query.skus
            ) {
                $('[data-iwptp-ck="query_selected"]').addClass('iwptp-done');
            } else {
                $('[data-iwptp-ck="query_selected"]').removeClass('iwptp-done');
            }
        })
        //-- column_element_created
    $('body').on('change', '.iwptp-editor > [iwptp-model-key="columns"]', function() {
        var column_element_created = false;
        if (iwptp.data.columns.laptop.length) {
            $.each(iwptp.data.columns.laptop, function(i_col, col) {
                // heading content element
                if (col.heading.content[0].elements.length) {
                    column_element_created = true;
                    return false;
                }
                // cell template[row] element
                $.each(col.cell.template, function(i_row, row) {
                    if (row.elements.length) {
                        column_element_created = true;
                        return false;
                    }
                })
            })
        }

        if (column_element_created) {
            $('[data-iwptp-ck="column_element_created"]').addClass('iwptp-done');
        } else {
            $('[data-iwptp-ck="column_element_created"]').removeClass('iwptp-done');
        }
    })

    // save JSON data to server
    controller.save_data = function(e) {
        var googleFonts = [];

        $('.iwptp-tab-content-item[data-content="style"] input, .iwptp-tab-content-item[data-content="style"] select').trigger('change');

        if ($('.iwptp-font-family-dropdown').length > 0) {
            $.each($('.iwptp-font-family-dropdown'), function() {
                if ($(this).val() !== '') {
                    googleFonts.push($(this).val());
                }
            })
        }

        e.preventDefault();

        iwptpLoadingStart();

        if ($('.iwptp-element-settings .iwptp-block-editor-lightbox-screen:visible').length > 0) {
            $('.iwptp-element-settings .iwptp-block-editor-lightbox-screen:visible').find('.iwptp-block-editor-lightbox-back').trigger('click');
        }

        // ensure change is triggered on any focused input
        var $focused_input = $('input:focus, textarea:focus');
        if ($focused_input.length && $focused_input.attr('iwptp-model-key')) {
            $focused_input.trigger('change');
        }

        $('body').trigger('iwptp_save');
        data.version = window.iwptp_version;

        var $this = $(this), // form
            post_id = $this.find("input[name='post_id']").val(),
            title = $this.find("input[name='title']").val(),
            nonce = $this.find("input[name='nonce']").val(),
            json_data = JSON.stringify(data),
            $button = $this.find(".iwptp-save"),
            action = $this.attr('action');

        if (!$this.hasClass("iwptp-saving")) {
            $.ajax({
                type: "POST",
                url: ajaxurl,

                beforeSend: function() {
                    $this.addClass("iwptp-saving");
                    $button.addClass("disabled");
                },

                data: {
                    action: action,
                    iwptp_post_id: post_id,
                    iwptp_title: title,
                    iwptp_nonce: nonce,
                    iwptp_data: json_data,
                    fonts: googleFonts
                },

                success: function(data) {
                    $this.removeClass("iwptp-saving");
                    $button.removeClass("disabled");
                    // success
                    if (typeof data == 'string' && -1 !== data.indexOf("IWPTPL success:")) {
                        iwptpLoadingSuccess();

                        setTimeout(function() {
                            $('.sweet-alert:visible').find('button.confirm').trigger('click')
                        }, 800)
                    } else {
                        iwptpLoadingError();

                        // console.log(data);
                    }
                }
            });
        }
    }

    /* dynamic input wrapper */

    controller.open_dynamic_input_wrapper = function() {
        var $input = $(this);

        if (
            $input.parent().hasClass('iwptp-diw') ||
            $input.hasClass('iwptp-diw--disabled')
        ) {
            return;
        }

        var prev_style = $input.attr('style'),
            style = { 'width': $input.outerWidth() };

        $.each(['float', 'margin', 'top', 'right', 'bottom', 'left'], function(key, prop) {
            style[prop] = $input.css(prop);
        })

        if ($input.css('position') == 'absolute') {
            style['position'] = 'absolute';
        }

        var $wrap = $('<div class="iwptp-diw">')
        $wrap.css(style);
        $input.wrap($wrap);

        $input.focus();

        $('body').on('blur mousedown keydown', controller.close_dynamic_input_wrapper);

        $input.after('<div class="iwptp-diw-tray">');
        var $tray = $input.next('.iwptp-diw-tray');

        if (
            $input.attr('iwptp-model-key').indexOf('color') !== -1 ||
            $input.attr('iwptp-model-key') == 'background' ||
            $input.attr('iwptp-model-key') == 'fill'
        ) {
            $tray.append('<input type="color">');
            $tray.css({ 'height': 0 });
            var $color = $tray.find('input[type="color"]');

            $color
                .spectrum({
                    color: $input.val(),
                    flat: true,
                    allowEmpty: true,
                    showAlpha: true,
                    preferredFormat: 'rgba',
                    clickoutFiresChange: true,
                    showInput: false,
                    showButtons: false,
                    move: function(color) {
                        $input.val(color.toRgbString()).change();
                    }
                })
        }
    }

    controller.close_dynamic_input_wrapper = function(e) {
        var $origin = $(e.target),
            $wrap = $origin.closest('.iwptp-diw');

        if (!$wrap.length) {
            $('.iwptp-diw').each(function() {
                var $this = $(this),
                    $input = $this.children('input');

                $this.replaceWith($input);
                $('body').off('blur mousedown keydown', controller.close_dynamic_input_wrapper);

                $input.change();
            })
        }
    }

    /* increase/decrease number with arrow keys */
    $('body').on('keydown', 'input[iwptp-model-key]', function(e) {
        if (!e.key || -1 === $.inArray(e.key, ['ArrowUp', 'ArrowDown'])) {
            return;
        }

        if (-1 === $.inArray($(this).attr('iwptp-model-key'), [
                'font-size', // permitted
                'custom_zoom_scale',
                'line-height',
                'letter-spacing',
                'stroke-width',
                'top', 'left', 'right', 'bottom',
                'width', 'max-width', 'min-width',
                'height', 'max-height', 'min-height',
                'border-radius', 'border-width', 'border-left-width', 'border-right-width', 'border-top-width', 'border-bottom-width',
                'divider-border-width',
                'padding', 'padding-left', 'padding-right', 'padding-top', 'padding-bottom',
                'section-padding', 'section-padding-left', 'section-padding-right', 'section-padding-top', 'section-padding-bottom',
                'margin', 'margin-left', 'margin-right', 'margin-top', 'margin-bottom',
                'gap', 'row_gap'
            ])) {
            return;
        }

        if (!e.target.value) {
            e.target.value = '0px';
        }

        var suffix = e.target.value.slice(-2),
            val = e.target.value;

        if (val.length > 2 && isNaN(suffix)) {
            val = val.substring(0, val.length - 2);
        } else {
            suffix = '';
        }

        var is_float = !((parseInt(val) + '').length === (val + '').length);

        if (e.key == 'ArrowUp') {
            e.target.value = (((val * 10) + (is_float ? 1 : 10)) / 10);
        } else if (e.key == 'ArrowDown') {
            e.target.value = (((val * 10) - (is_float ? 1 : 10)) / 10);
        }

        // convert '2' back to float: '2.0'
        val = e.target.value;
        if (is_float) {
            if (((parseInt(val) + '').length === (val + '').length)) { // float turned to int, let's fix this
                e.target.value = e.target.value + '.0';
            }
        }

        e.target.value += suffix;

    })

    /* attach event handlers */

    // switch editor tabs
    $('body').on('click', '.iwptp-tab-label', controller.switch_editor_tabs);

    // title
    $('body').on('keyup', '.iwptp-table-title', controller.update_table_title);

    // toggle sub categories
    $('body').on('click', '.iwptp-toggle-sub-categories', controller.toggle_sub_categories);

    // dynamic input wrapper
    $('body').on('focus', 'input[type="text"][iwptp-model-key], input[type="number"][iwptp-model-key]', controller.open_dynamic_input_wrapper);

    // auto select
    $('body').on('click', '.iwptp-auto-select-on-click', controller.auto_select_on_click);

    // data hook up
    dominator_ui.init($('.iwptp-editor, .iwptp-settings'), data);

    // other toggle
    $('body').on('click', '.iwptp-toggle-label', function() {
        var $this = $(this),
            $container = $this.closest('.iwptp-toggle-options'),
            toggle = $container.attr('iwptp-model-key');

        $container.toggleClass('iwptp-open');
    });

    // toggle option rows
    $('body').on('click', '.iwptp-editor-row-handle', function(e) {
        var $target = $(e.target),
            $row = $target.closest('.iwptp-editor-row');
        if (!$target.closest('.iwptp-editor-row-no-toggle').length &&
            $target.closest('.iwptp-editor-row-handle-data, .iwptp-editor-row-toggle').length
        ) {
            $row.toggleClass('iwptp-editor-row-toggle-opened');
        }
    });

    // toggle
    $('body').on('click', '.iwptp-toggle > .iwptp-toggle-trigger', function(e) {
        var $toggle = $(this).closest('.iwptp-toggle');
        $toggle.toggleClass('iwptp-toggle-on iwptp-toggle-off');
        $('body').off('click.iwptp_toggle_blur');
        if ($toggle.hasClass('iwptp-toggle-on')) {
            // blurrable toggle is opened
            // close on blur
            // add this to array
            $('body').on('click.iwptp_toggle_blur', function(e) {
                if (!$(e.target).closest($toggle).length) {
                    $toggle.children('.iwptp-toggle-trigger').click();
                    $('body').off('click.iwptp_toggle_blur');
                }
            })
        }
    }).on('click', '.iwptp-toggle-x', function(e) {
        $(this).closest('.iwptp-toggle').toggleClass('iwptp-toggle-on iwptp-toggle-off');
    })

    // resume editor tab
    if (window.location.hash) {
        $('[data-iwptp-tab="' + window.location.hash.substring(1) + '"].iwptp-tab-label').trigger('click');
        $('.iwptp-settings > [iwptp-model-key="' + window.location.hash.substring(1) + '"] > .iwptp-toggle-label').trigger('click');
    }

    // submit
    // -- button click
    $('body').on('submit', '.iwptp-save-data', controller.save_data);
    // -- keyboard: Ctrl/Cmd + s
    $(window).bind('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && String.fromCharCode(e.which).toLowerCase() === 's') {
            e.preventDefault();

            setTimeout(function() {
                $('.iwptp-save-data').submit();
            }, 50)
        }
    });

    // column link scroll
    $('body').on('click', '.iwptp-column-links a', function(e) {
        e.preventDefault();

        var $this = $(this),
            $device_columns = $this.closest('.iwptp-editor-columns-container'),
            column_index = $this.attr('data-iwptp-index'),
            $target = $device_columns.children('[iwptp-model-key-index="' + column_index + '"]'),
            $heading = $('> .iwptp-editor-light-heading', $device_columns),
            offset = $heading.outerHeight() + 135;

        if (column_index == 'add') {
            var $target = $device_columns.find('.iwptp-columns-add-row-button-main[iwptp-add-row-template]');
            offset += 40;
            $target.click();
        }

        $([document.documentElement, document.body]).animate({
            scrollTop: $target.offset().top - offset
        }, 300, 'linear');
    })

    // column name input toggle
    //-- open
    $('body').on('click', '.iwptp-column-index', function() {
            var $this = $(this);
            $this
                .addClass('iwptp-column-index--input-on')
                .find('input').focus()
                .end().find('.iwptp-diw input').focus(); // diw workaround
        })
        //-- close
    $('body').on('click', '.iwptp-close-column-name-input', function(e) {
        var $this = $(this),
            $input = $this.prev('input');

        $input.val('').change();
        e.stopPropagation();
    })

    // columns toggle
    //-- central buttons
    $('body').on('click', '.iwptp-device-columns-toggle', function(e) {
            var $this = $(this),
                $device_columns = $this.closest('.iwptp-editor-columns-container'),
                $columns = $device_columns.find('.iwptp-column-settings'),
                $heading = $('> .iwptp-editor-light-heading', $device_columns),
                offset = 50;

            if ($(e.target).closest('.iwptp-device-columns-toggle__expand').length) {
                $columns.addClass('iwptp-toggle-column-expand');
            } else if ($(e.target).closest('.iwptp-device-columns-toggle__contract').length) {
                $columns.removeClass('iwptp-toggle-column-expand');
            }

            $([document.documentElement, document.body]).animate({
                scrollTop: 0
            }, 300, 'linear');

            e.preventDefault();
        })
        //-- column buttons
    $('body').on('click', '.iwptp-editor-row-expand, .iwptp-editor-row-contract', function(e) {
            var $this = $(this),
                $column = $this.closest('.iwptp-column-settings');

            if ($this.hasClass('iwptp-editor-row-expand')) {
                $column.addClass('iwptp-toggle-column-expand');
            } else {
                $column.removeClass('iwptp-toggle-column-expand');
            }

            e.preventDefault();
        })
        //-- column body
    $('body').on('click', '.iwptp-column-toggle-capture', function(e) {
        var $column = $(this).closest('.iwptp-column-settings');
        $column.toggleClass('iwptp-toggle-column-expand');
    })

    // dev's little helper
    window.iwptp_duplicate_laptop_to_phone = function() {
        iwptp_duplicate_device('laptop', 'phone');
    }

    window.iwptp_duplicate_laptop_to_tablet = function() {
        iwptp_duplicate_device('laptop', 'tablet');
    }

    window.iwptp_duplicate_device = function(source, destination) {
        if (!source) {
            source = 'laptop';
        }

        if (!destination) {
            destination = 'phone';
        }

        $.each(data.columns[source], function(index, col) {
            data.columns[destination].push(dominator_ui.refresh_ids($.extend(true, {}, col)));
        })

        $('.iwptp-editor-save-button').click();

        window.location.reload();
    }

    // settings

    //-- reset settings

    $('body').on('click', '.iwptp-reset-global-settings', function(e) {
        if (window.confirm('Are you sure you want to reset IWPTPL global settings? This will not delete your tables. It will only reset the global settings for this plugin.')) {
            return;
        }
        e.preventDefault();
    })

    // shortcode otions
    var $shortcode_ops = $('.iwptp-shortcode-info'),
        $pro_op_row = $('tr', $shortcode_ops).filter(function() {
            var $this = $(this);
            return $this.find('.iwptp-pro-badge').length;
        }),
        $pro_msg_row = $('<tr><td colspan="2">Following are all <span class="iwptp-pro-badge">PRO</span> version options:</td></tr>');

    if ($pro_op_row.length) {
        $('td', $pro_msg_row).css({
            'font-size': '22px',
            'font-weight': 'bold',
            'text-transform': 'capitalize',
            'padding': '40px 10px'
        });

        $.merge($pro_msg_row, $pro_op_row).appendTo('tbody', $shortcode_ops);
    }
});