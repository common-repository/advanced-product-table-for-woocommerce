(function($, view) {

    view.parent;

    view.render = function() {

        var $be = this.parent.$elm,
            parent = this.parent,
            view = this,
            data = this.parent.model.get_data();

        $be.empty(); // make blank
        let buttons = '<button type="button" class="iwptp-element-block-duplicate" title="Duplicate"><span class="iwptp-icon iwptp-icon-copy"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-copy"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg></span></button>' +
            '<button type="button" class="iwptp-element-block-delete" title="Delete"><span class="iwptp-icon iwptp-icon-trash"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></span></button>';

        $.each(data, function(index, row) {

            // create row
            var $row = $('<div class="iwptp-block-editor-row" data-id="' + row.id + '">').data('iwptp-data', row);

            // append elements to row
            $.each(row.elements, function(el_index, element) {
                var $element = $('<div class="iwptp-element-block" data-type="' + element.type + '" data-id="' + element.id + '">' + view.get_label(element) + buttons + '</div>');
                $row.append($element.data('iwptp-data', element));
            })

            // add element trigger
            var $add_element = $('<a href="javascript:;" class="iwptp-block-editor-add-element">+ Add Element</a>');
            $row.append($add_element);

            // edit row trigger
            if (parent.config.edit_row_partial) {
                var icon = $('#iwptp-icon-sliders').length ? $('#iwptp-icon-sliders').text() : '*',
                    $settings = $('<span class="iwptp-block-editor-edit-row" title="Edit row settings">' + icon + '</span>');
                $row.append($settings);
            }

            // delete row trigger
            if (
                parent.config.delete_row &&
                (
                    data.length > 1 || // multiple rows
                    (
                        typeof data[0].elements !== 'undefined' &&
                        data[0].elements.length
                    )
                )
            ) {
                var icon = $('#iwptp-icon-x').length ? $('#iwptp-icon-x').text() : 'x',
                    $del = $('<span class="iwptp-block-editor-delete-row" title="Delete row">' + icon + '</span>');
                $row.append($del);
            }

            // append row to editor
            $be.append($row);
        });

        // add row trigger
        if (this.parent.config.add_row) {
            var $add_row = $('<a href="#" class="iwptp-block-editor-add-row">+ Add Row</a>');
            $be.append($add_row);
        }

        // make rows sortable
        if ($('.iwptp-block-editor-row', $be).length > 1) {
            $be.sortable({
                items: '.iwptp-block-editor-row',
                disabled: false,
            });
        } else {
            $be.sortable({
                items: '.iwptp-block-editor-row',
                disabled: true,
            });
        }

        // connect with
        var cw = this.parent.config.connect_with,
            $lb = $be.closest('.iwptp-block-editor-lightbox-screen');
        if ($lb.length) {
            cw = '[data-partial="' + $lb.attr('data-partial') + '"].iwptp-block-editor-lightbox-screen ' + cw;
        }

        // make blocks sortable
        $('.iwptp-block-editor-row', $be).sortable({
            items: '.iwptp-element-block',
            connectWith: cw,
            placeholder: 'iwptp-element-block-placeholder',
            forcePlaceholderSize: true,
            start: function(event, ui) {
                // helper size
                ui.helper.width(ui.helper.width() + 1).height('');

                // placeholder width
                ui.placeholder.width(ui.item.outerWidth()).addClass('iwptp-element-block');

                if (ui.helper.closest('#iwptp-editor-left-sidebar').length < 1 && $('.iwptp-block-editor-lightbox-screen').length === 1) {
                    $('.iwptp-block-editor-lightbox-screen').remove();
                    jQuery('.iwptp-left-sidebar-help').show();
                }
            },
            sort: function(event, ui) {}
        });

    }

    view.lightbox = function(options) {
        jQuery('.iwptp-left-sidebar-help').hide();
        var default_ops = {
            $element: null,
            duplicate_remove: true,
            attr: {}
        };

        $.extend(true, default_ops, options);

        // create
        var $lightbox = $('<div class="iwptp-block-editor-lightbox-screen"><div class="iwptp-block-editor-lightbox-content"><div class="iwptp-block-editor-lightbox-content-rows"></div></div></div>'),
            close_icon = $('#iwptp-icon-x').length ? $('#iwptp-icon-x').text() : '',
            $close = $('<span class="iwptp-block-editor-lightbox-close" title="Close">' + close_icon + '</span>');

        if ($('body').hasClass('iwptp-editor-left-sidebar-hide')) {
            $('#iwptp-left-sidebar-toggle').trigger('click')
        }

        $('.iwptp-element-settings').append($lightbox);

        $lightbox
            .data('iwptp-block-element', options.$element ? options.$element : '')
            .attr({
                'iwptp-controller': 'edit-element-lightbox',
                'iwptp-model-key': 'block_element_data',
            })
            .attr(options.attr)
            .find('.iwptp-block-editor-lightbox-content-rows')
            .append($('script[data-iwptp-partial="' + options.partial + '"]').text())
            .end()
            .show();

        if ($('.iwptp-element-settings').find('.iwptp-block-editor-lightbox-screen').length > 1) {
            $('.iwptp-block-editor-lightbox-screen').css({
                opacity: 0
            })
            $('.iwptp-block-editor-lightbox-screen').last().css({
                opacity: 1
            })
        } else {
            $('.iwptp-block-editor-lightbox-screen').css({
                opacity: 1
            })
        }

        iwptpFixElementSettingsSize();

        // add modal flag
        $('body').addClass('iwptp-be-lightbox-on');

        setTimeout(function() {
            if ($('.iwptp-select2').length > 0) {
                $('.iwptp-select2').select2();

                setTimeout(function() {
                    if ($('.select2-container').length > 0) {
                        $('.select2-container').css({ width: "100%" })
                    }
                }, 250)
            }
        }, 200);

        // destroy
        // -- via screen click
        var _ = this;
        $lightbox.on('click', function(e) {
            if ($(e.target).is($lightbox)) {
                $lightbox.trigger('destroy');
            }
        })

        // -- via close 'X' click
        $('> .iwptp-block-editor-lightbox-content > .iwptp-block-editor-lightbox-content-rows > h2 > .iwptp-block-editor-lightbox-close, > .iwptp-block-editor-lightbox-content > .iwptp-block-editor-lightbox-content-rows > h2 > .iwptp-block-editor-lightbox-back', $lightbox).on('click', function() {
            $lightbox.trigger('destroy');
        })

        // -- destroy event handler
        $lightbox.on('destroy', function() {
            $lightbox.change();
            $lightbox.remove();

            if ($('.iwptp-element-settings').find('.iwptp-block-editor-lightbox-screen').length > 0) {
                $.each($('.iwptp-element-settings').find('.iwptp-block-editor-lightbox-screen'), function() {
                    let partial = $(this).attr('data-partial');
                    if (partial === undefined) {
                        $(this).change().remove();
                    }
                })

                $('.iwptp-block-editor-lightbox-screen').css({
                    opacity: 0
                });

                $('.iwptp-block-editor-lightbox-screen').last().css({
                    opacity: 1
                });
            } else {
                if (!$('.iwptp-block-editor-lightbox-screen').length) {
                    $('body').removeClass('iwptp-be-lightbox-on');

                    jQuery('.iwptp-left-sidebar-help').show();
                } else {
                    $('.iwptp-block-editor-lightbox-screen').css({
                        opacity: 1
                    })
                }
            }
        })

        // search
        var $list = $('.iwptp-block-editor-element-type-list', $lightbox),
            $search_input = $('.iwptp-block-editor-element-type-list__search__input', $list),
            $elm_button = $('.iwptp-block-editor-element-type', $list);

        $search_input.on('keyup', function() {
            var $this = $(this),
                val = $this.val().trim();

            if (!val) {
                $elm_button.show();
                return;
            }

            $elm_button.each(function() {
                var $this = $(this),
                    label = $this.text().toLowerCase().trim();
                if (label.indexOf(val) == -1) {
                    $this.hide();
                } else {
                    $this.show();
                }
            })
        })

        $search_input.focus();

        return $lightbox;
    }

    view.get_label = function(element) {

            var type_unslug = element.type.replace(/(_|^)([^_]?)/g, function(_, prep, letter) {
                    return (prep && ' ') + letter.toUpperCase();
                }),
                label = type_unslug;

            switch (element.type) {
                case 'attribute':
                case 'attribute_filter':
                    if (element.attribute_name) {

                        if (
                            element.attribute_name == '_custom' &&
                            element.custom_attribute_name
                        ) {
                            label = element.custom_attribute_name + ' (custom)';

                        } else {
                            label = element.attribute_name;
                            if (typeof window.iwptp_attributes == 'object') {
                                $.each(window.iwptp_attributes, function(key, val) {
                                    if (val.attribute_name == element.attribute_name) {
                                        label = val.attribute_label;
                                    }
                                })
                            }
                        }

                        label = 'Attribute: <span>' + view.sanitize(label.charAt(0).toUpperCase() + label.substr(1)) + '</span>';
                    }
                    break;

                case 'custom_field':
                case 'custom_field_filter':
                    if (element.field_name) {
                        label = 'Custom field: <span>' + view.sanitize(element.field_name) + '</span>';
                    }
                    break;

                case 'taxonomy':
                case 'taxonomy_filter':
                    if (element.taxonomy) {
                        label = 'Taxonomy: <span>' + view.sanitize(element.taxonomy) + '</span>';
                    }
                    break;

                case 'text':
                case 'text__col':
                    if (element.text) {
                        label = view.sanitize(element.text);
                        if (label.length > 30) {
                            label = label.substring(0, 30) + '...';
                        }
                        label = 'Text: <span>"' + label + '"<span>';
                    } else {
                        label = 'Text';
                    }
                    break;

                case 'html':
                case 'html__col':
                    if (element.html) {
                        label = view.sanitize(element.html);
                        if (label.length > 20) {
                            label = label.substring(0, 20) + '...';
                        }
                        label = 'HTML: <span>"' + label + '"<span>';

                    } else {
                        label = 'HTML';
                    }

                    break;

                case 'shortcode':
                    if (element.shortcode) {
                        var shortcode = element.shortcode;
                        if (shortcode.length > 30) {
                            shortcode = shortcode.substring(0, 30) + '...';
                        }

                        label = 'Shortcode: <span>' + view.sanitize(shortcode) + '</span>';
                    }
                    break;

                case 'sku':
                    label = 'SKU';
                    break;

                case 'sl':
                    label = 'SL';
                    break;

                case 'short_message':
                    label = 'Short Message';
                    break;

                case 'date_range':
                    label = 'Date range';
                    break;

                case 'full_screen':
                    label = 'Full screen';
                    break;

                case 'download_csv':
                    label = 'Download CSV';
                    break;

                case 'media_image':
                case 'media_image__col':
                    label = 'Media Image';
                    break;

                case 'product_id':
                    label = 'Product ID';
                    break;

                case 'sorting':
                    if (element.orderby) {
                        label = 'Sort by: <span>';

                        if (element.orderby == 'meta_value_num' || element.orderby == 'meta_value') {
                            label += ' CF - ' + element.meta_key;

                        } else if (element.orderby == 'id') {
                            label += ' Product ID';

                        } else if (element.orderby == 'sku') {
                            label += ' SKU as text';

                        } else if (element.orderby == 'sku_num') {
                            label += ' SKU as integer';

                        } else if (-1 !== $.inArray(element.orderby, ['attribute', 'attribute_num']) &&
                            element.orderby_attribute
                        ) {
                            var attribute_label = element.orderby_attribute;
                            $.each(iwptp_attributes, function(index, attribute) {
                                if ('pa_' + attribute.attribute_name == element.orderby_attribute) {
                                    attribute_label = attribute.attribute_label;
                                    return false;
                                }
                            })

                            label += ' Attribute - ' + view.sanitize(attribute_label);

                        } else {
                            label += element.orderby[0].toUpperCase() + element.orderby.substring(1);

                        }

                        label += '</span>';

                    }
                    break;

                case 'search':
                    if (element.target && Array.isArray(element.target) && element.target.length) {
                        if (element.target.length == 1) {
                            var field = element.target[0];
                            if (
                                field == 'attribute' &&
                                element.attributes &&
                                element.attributes.length
                            ) {
                                field += ': ' + element.attributes.join(", ");
                            }

                            if (
                                field == 'custom_field' &&
                                element.custom_fields &&
                                element.custom_fields.length
                            ) {
                                field += ': ' + element.custom_fields.join(", ");
                            }

                        } else {
                            var mixed_fields = element.target.join(", ");
                            if (mixed_fields.length > 45) {
                                mixed_fields = mixed_fields.substring(0, 45) + '...';
                            }
                            var field = "mixed " + element.target.length + " (" + mixed_fields + ")";

                        }
                        field = view.sanitize(field);

                        if (field.length > 70) {
                            field = field.substring(0, 70) + '...';
                        }

                        label = 'Search: <span>' + field + '</span>';
                    }

                    break;

                case 'apply_reset':
                    label = 'Apply / Reset';
                    break;

                case 'regular_price__on_sale':
                    console.log('here');
                    label = 'Regular price';
                    break;

                case 'icon':
                case 'icon__col':
                    if (element.name) {
                        label = '<img class="iwptp-icon-rep" src="' + iwptp_icons + element.name + '.svg">';
                    }
                    break;

                case 'dot':
                case 'dot__col':
                    label = '⋅';
                    break;

                case 'select_variation':
                    label = 'Select variation';

                    // radio single
                    if (typeof element.display_type == 'undefined' || element.display_type == 'radio_single') {

                        if (element.variation_name) {
                            var name = view.sanitize(element.variation_name);
                            label = 'Select variation: <span>' + name + '</span>';

                        } else {
                            label = 'Select variation: <span>*Single variation*</span>';
                        }

                        // radio multiple
                    } else if (element.display_type == 'radio_multiple') {
                        label = 'Select variation: <span>*Radio buttons*</span>';

                        // dropdown
                    } else if (element.display_type == 'dropdown') {
                        label = 'Select variation: <span>*Dropdown*</span>';
                    }

                    break;

            }

            if (
                element.type.split('__').length == 2 &&
                -1 == $.inArray(element.type, ['text__col', 'html__col', 'media_image__col', 'icon__col', 'dot__col'])
            ) {
                var string = element.type.split('__')[0];
                label = string.charAt(0).toUpperCase() + string.slice(1).replace('_', ' ');

            }

            return label;
        },

        view.mark_elm = function(row_index, elm_index) {
            var $be = this.parent.$elm,
                $row = $be.children('.iwptp-block-editor-row').eq(row_index),
                $target;

            if ($row.length) {
                var $elm = $row.children('.iwptp-element-block').eq(elm_index);

                if ($elm.length) {
                    $target = $elm;
                } else {
                    $target = $row;
                }
            }

            $target.addClass('iwptp-element-selected');

            setTimeout(function() {
                if ($('.iwptp-block-editor-lightbox-screen[data-row-index="' + row_index + '"][data-elm-index="' + elm_index + '"]').length < 1) {
                    $target.removeClass('iwptp-element-selected');
                }
            }, 100)
        }

    view.sanitize = function(str) {
        return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

})(jQuery, IWPTPL_Block_Editor.View);