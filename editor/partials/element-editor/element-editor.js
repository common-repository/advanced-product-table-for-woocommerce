jQuery(function ($) {

    dominator_ui.controllers.search_rules = function ($elm, data, e) {
        if (undefined !== data.enabled) {
            if (data.enabled) {
                $elm.addClass('iwptp-search-enabled');
                $elm.removeClass('iwptp-search-disabled');
            } else {
                $elm.addClass('iwptp-search-disabled');
                $elm.removeClass('iwptp-search-enabled');
            }
        }

        if (undefined !== data.items) {
            var total_enabled = 0;

            $.each(data.items, function (index, item) {
                if (
                    item &&
                    item.enabled
                ) {
                    ++total_enabled;
                }
            })

            $elm.children('.iwptp-search__enabled-count')
                .text(total_enabled + ' / ' + data.items.length)
                .removeClass('iwptp-search__enabled-count--all iwptp-search__enabled-count--none');

            if (data.items.length == total_enabled) {
                $elm.children('.iwptp-search__enabled-count').addClass('iwptp-search__enabled-count--all');
            } else if (!total_enabled) {
                $elm.children('.iwptp-search__enabled-count').addClass('iwptp-search__enabled-count--none');
            }

            $('.iwptp-search__enable-all, .iwptp-search__disable-all', $elm).removeClass('iwptp-search__disable');

            if (total_enabled == data.items.length) {
                $('.iwptp-search__enable-all', $elm).addClass('iwptp-search__disable');
            }

            if (!total_enabled) {
                $('.iwptp-search__disable-all', $elm).addClass('iwptp-search__disable');
            }
        }

        if (e) {
            // get

        } else {
            // set
            $('.iwptp-search__enable-all, .iwptp-search__disable-all', $elm)
                .off('click.dom_ui')
                .on('click.dom_ui', function (e) {
                    e.preventDefault();

                    if ($(e.target).hasClass('iwptp-search__disable')) {
                        return;
                    }

                    $.each(data.items, function (index, item) {
                        item.enabled = $(e.target).hasClass('iwptp-search__enable-all');
                    })
                    dominator_ui.init($elm, data);
                })

        }
    }

    dominator_ui.controllers.laptop_navigation = function ($elm, data, e) {

        var filters = [];

        // ensure header
        if (typeof data.header === 'undefined') {
            data.header = {
                rows: []
            };
        }

        // ensure the correct 'position' for left_sidebar elements
        if (data.header.rows.length) {
            // each header row
            $.each(data.header.rows, function (index, row) {
                $.each(row.columns, function (c_index, column) {
                    if (
                        column.template &&
                        typeof column.template[0] !== 'undefined' &&
                        column.template[0].elements &&
                        column.template[0].elements.length
                    ) {
                        $.each(column.template[0].elements, function (index, filter) {
                            filters.push($.extend({}, filter));
                            if (
                                typeof filter.position == 'undefined' ||
                                filter.position === 'left_sidebar'
                            ) {
                                filter.position = 'header'; // upward change
                                $('[data-id="' + filter.id + '"]').data('iwptp-data', $.extend({}, filter)); // change on block element
                            }
                        })
                    }
                })
            })
        }

        // ensure left_sidebar
        if (typeof data.left_sidebar === 'undefined') {
            data.left_sidebar = [];
        }

        // ensure the correct 'position' for left_sidebar elements
        if (
            data.left_sidebar.length &&
            data.left_sidebar[0].elements.length
        ) {
            $.each(data.left_sidebar[0].elements, function (index, filter) {
                filters.push($.extend({}, filter));
                if (
                    typeof filter.position == 'undefined' ||
                    filter.position === 'header'
                ) {
                    filter.position = 'left_sidebar'; // upward change
                    $('[data-id="' + filter.id + '"]').data('iwptp-data', $.extend({}, filter)); // change on block element
                }
            })
        }

        // detect duplicate filters
        var errors = [],
            multi_permitted = ['attribute_filter', 'custom_field_filter', 'taxonomy_filter', 'search', 'html', 'text', 'icon', 'space', 'tooltip', 'media_image'],
            used_singular_filters = {},
            used_attribute_filters = {},
            used_custom_field_filters = {},
            used_taxonomy_filters = {};

        $.each(filters, function (index, filter) {
            if (!filter.type) {
                return; // continue;
            }

            if ($.inArray(filter.type, multi_permitted) === -1) {
                if (typeof used_singular_filters[filter.type] === 'undefined') {
                    used_singular_filters[filter.type] = 1;
                } else {
                    used_singular_filters[filter.type] = used_singular_filters[filter.type] + 1;
                }

            } else {
                // multiple instances allowed for these filters but not with same settings
                switch (filter.type) {
                    case 'attribute_filter':

                        if (typeof filter.attribute_name !== 'undefined') {
                            if (typeof used_attribute_filters[filter.attribute_name] === 'undefined') {
                                used_attribute_filters[filter.attribute_name] = 1;
                            } else {
                                used_attribute_filters[filter.attribute_name] = used_attribute_filters[filter.attribute_name] + 1;
                            }
                        }

                        break;

                    case 'custom_field_filter':

                        if (typeof filter.field_name !== 'undefined') {
                            if (typeof used_custom_field_filters[filter.field_name] === 'undefined') {
                                used_custom_field_filters[filter.field_name] = 1;
                            } else {
                                used_custom_field_filters[filter.field_name] = used_custom_field_filters[filter.field_name] + 1;
                            }
                        }

                        break;

                    case 'taxonomy_filter':

                        if (typeof filter.taxonomy !== 'undefined') {
                            if (typeof used_taxonomy_filters[filter.taxonomy] === 'undefined') {
                                used_taxonomy_filters[filter.taxonomy] = 1;
                            } else {
                                used_taxonomy_filters[filter.taxonomy] = used_taxonomy_filters[filter.taxonomy] + 1;
                            }
                        }

                        break;

                    default:
                }

            }
        })

        // gather singular filter errors
        $.each(used_singular_filters, function (filter, count) {
            if (count > 1) {

                switch (filter) {
                    case 'download_csv':
                        filter = 'Download CSV';
                        break;

                    default:
                        filter = (filter.charAt(0).toUpperCase() + filter.slice(1)).replace(/_/g, " "); // uppercase first char and replace _ with space
                }

                errors.push('You are using "' + filter + '" ' + count + ' times. Please use only once to avoid errors.');
            }
        })

        // gather attribute filter errors
        $.each(used_attribute_filters, function (attribute_name, count) {
            if (count > 1) {
                errors.push('You are using the "Attribute filter" with the attribute "' + attribute_name + '" ' + count + ' times. Please use only once to avoid errors.');
            }
        })

        // gather custom field filter errors
        $.each(used_custom_field_filters, function (field_name, count) {
            if (count > 1) {
                errors.push('You are using the "Custom field filter" with the field name "' + field_name + '" ' + count + ' times. Please use only once to avoid errors.');
            }
        })

        // gather taxonomy filter errors
        $.each(used_taxonomy_filters, function (taxonomy, count) {
            if (count > 1) {
                errors.push('You are using the "Taxonomy filter" with the taxonomy "' + taxonomy + '" ' + count + ' times. Please use only once to avoid errors.');
            }
        })

        if (!errors.length) {
            $('.iwptp-navigation-errors').hide();

        } else {
            $('.iwptp-navigation-errors').show();
            $('.iwptp-navigation-errors .iwptp-navigation-errors__warning').remove();
            var errors = '<li class="iwptp-navigation-errors__warning">' + errors.join('</li><li class="iwptp-navigation-errors__warning">') + '</li>';
            $('.iwptp-navigation-errors .iwptp-navigation-errors__warnings').html(errors);

        }

        if (e) { // get

        } else { // set

        }
    }

    dominator_ui.controllers.taxonomy_terms = function ($elm, data, e) {

        if (e) { // get

        } else { // set
            var $term = $('[iwptp-model-key="term"]'),
                $taxonomies = $('[iwptp-model-key="taxonomy"]');

            $taxonomies.off('change.iwptp_get_terms').on('change.iwptp_get_terms', function () {
                var $this = $(this),
                    taxonomy = $this.val(),
                    $term = $this.siblings('[iwptp-model-key="term"]'),
                    term = $term.data('iwptp-data'),
                    $loading = $this.siblings('.iwptp-loading-term');

                if ($term.attr('data-iwptp-for-taxonomy') == taxonomy) {
                    return;
                }

                $.ajax({
                    type: "POST",
                    url: ajaxurl,

                    beforeSend: function () {
                        $term.attr('data-iwptp-for-taxonomy', '').html('<option>Term</option>').trigger('change').hide();
                        $loading.show();
                    },

                    data: {
                        action: 'iwptp_get_attribute_terms',
                        nonce: IWPTP_DATA.ajax_nonce,
                        taxonomy: taxonomy,
                    },

                    success: function (data) {
                        if (!data || data == 0) {
                            $loading.hide();
                            $term.show();
                            return;
                        }

                        $term.attr('data-iwptp-for-taxonomy', taxonomy);
                        $term.html('<option value="">Select a term</option>');

                        $.each(data, function (key, term) {
                            $term.append('<option value="' + term.slug + '">' + term.name + '</option>');
                        })

                        if (term) {
                            $term.val(term);
                        }

                        $term.show();
                        $loading.hide();
                    }
                });
            })

            $term.each(function () {
                var $this = $(this),
                    $taxonomy = $this.siblings('[iwptp-model-key="taxonomy"]'),
                    term = $this.data('iwptp-data');

                if ($taxonomy.val()) {
                    $taxonomy.change();
                }
            })

        }
    }

    dominator_ui.controllers.include_users = function ($elm, data, e) {
        if (e) { // get
            var $target = $(e.target)
            // upstream
            var $parent = $elm.data('iwptp-parent');
            $parent.data('iwptp-data')['include_users'] = $target.val();
            $parent.trigger('change');

        } else { // set
            var $parent = $elm.data('iwptp-parent');
            if (iwptpSelect2Data['users'] !== undefined && $parent.data('iwptp-data')['include_users'].length > 0) {
                $.each($parent.data('iwptp-data')['include_users'], function (key, userId) {
                    if (iwptpSelect2Data['users'][userId] !== undefined) {
                        $elm.append('<option value="' + userId + '">' + iwptpSelect2Data['users'][userId] + '</option>');
                    }
                })
            }
            $elm.val($parent.data('iwptp-data')['include_users']).trigger('change');
            $parent.trigger('change');
        }

        if (typeof iwptp_terms_cache != 'undefined' && iwptp_terms_cache.include_users) {
            delete iwptp_terms_cache.include_users;
        }
    }

    dominator_ui.controllers.include_products = function ($elm, data, e) {
        if (e) { // get
            var $target = $(e.target)
            // upstream
            var $parent = $elm.data('iwptp-parent');
            $parent.data('iwptp-data')['include_products'] = $target.val();
            $parent.trigger('change');

        } else { // set
            var $parent = $elm.data('iwptp-parent');
            if (iwptpSelect2Data['products'] !== undefined && $parent.data('iwptp-data')['include_products'].length > 0) {
                $.each($parent.data('iwptp-data')['include_products'], function (key, productId) {
                    if (iwptpSelect2Data['products'][productId] !== undefined) {
                        $elm.append('<option value="' + productId + '">' + iwptpSelect2Data['products'][productId] + '</option>');
                    }
                })
            }
            $elm.val($parent.data('iwptp-data')['include_products']).trigger('change');
            $parent.trigger('change');
        }

        if (typeof iwptp_terms_cache != 'undefined' && iwptp_terms_cache.include_products) {
            delete iwptp_terms_cache.include_products;
        }
    }

    dominator_ui.controllers.exclude_products = function ($elm, data, e) {
        if (e) { // get
            var $target = $(e.target)
            // upstream
            var $parent = $elm.data('iwptp-parent');
            $parent.data('iwptp-data')['exclude_products'] = $target.val();
            $parent.trigger('change');

        } else { // set
            var $parent = $elm.data('iwptp-parent');
            if (iwptpSelect2Data['products'] !== undefined && $parent.data('iwptp-data')['exclude_products'].length > 0) {
                $.each($parent.data('iwptp-data')['exclude_products'], function (key, productId) {
                    if (iwptpSelect2Data['products'][productId] !== undefined) {
                        $elm.append('<option value="' + productId + '">' + iwptpSelect2Data['products'][productId] + '</option>');
                    }
                })
            }
            $elm.val($parent.data('iwptp-data')['exclude_products']).trigger('change');
            $parent.trigger('change');
        }

        if (typeof iwptp_terms_cache != 'undefined' && iwptp_terms_cache.exclude_products) {
            delete iwptp_terms_cache.exclude_products;
        }
    }

    dominator_ui.controllers.include_taxonomies = function ($elm, data, e) {
        if (e) { // get
            var $target = $(e.target);
            let value = $target.val();

            // upstream
            var $parent = $elm.data('iwptp-parent');
            $parent.data('iwptp-data')['include_taxonomies'] = $target.val();
            $parent.trigger('change');

        } else { // set
            var $parent = $elm.data('iwptp-parent');
            if (iwptpSelect2Data['taxonomies'] !== undefined && $parent.data('iwptp-data')['include_taxonomies'].length > 0) {
                $.each($parent.data('iwptp-data')['include_taxonomies'], function (key, taxonomyId) {
                    if (iwptpSelect2Data['taxonomies'][taxonomyId] !== undefined) {
                        $elm.append('<option value="' + taxonomyId + '">' + iwptpSelect2Data['taxonomies'][taxonomyId] + '</option>');
                    }
                })
            }
            $elm.val($parent.data('iwptp-data')['include_taxonomies']).trigger('change');
            $parent.trigger('change');
        }

        if (typeof iwptp_terms_cache != 'undefined' && iwptp_terms_cache.include_taxonomies) {
            delete iwptp_terms_cache.include_taxonomies;
        }
    }

    dominator_ui.controllers.exclude_taxonomies = function ($elm, data, e) {
        if (e) { // get
            var $target = $(e.target)
            // upstream
            var $parent = $elm.data('iwptp-parent');
            $parent.data('iwptp-data')['exclude_taxonomies'] = $target.val();
            $parent.trigger('change');

        } else { // set
            var $parent = $elm.data('iwptp-parent');
            if (iwptpSelect2Data['taxonomies'] !== undefined && $parent.data('iwptp-data')['exclude_taxonomies'].length > 0) {
                $.each($parent.data('iwptp-data')['exclude_taxonomies'], function (key, taxonomyId) {
                    if (iwptpSelect2Data['taxonomies'][taxonomyId] !== undefined) {
                        $elm.append('<option value="' + taxonomyId + '">' + iwptpSelect2Data['taxonomies'][taxonomyId] + '</option>');
                    }
                })
            }
            $elm.val($parent.data('iwptp-data')['exclude_taxonomies']).trigger('change');
            $parent.trigger('change');
        }

        if (typeof iwptp_terms_cache != 'undefined' && iwptp_terms_cache.exclude_taxonomies) {
            delete iwptp_terms_cache.exclude_taxonomies;
        }
    }

    dominator_ui.controllers.device_columns = function ($elm, data, e) {

        // device column links
        $device_columns = $elm,
            $device_columns_heading = $('>.iwptp-editor-light-heading', $device_columns),
            columns = data || [];

        // re-render
        var $column_links = $('<div class="iwptp-column-links">'),
            i = 0;

        while (i < columns.length) {
            var name = columns[i].name ? columns[i].name : 'Col ' + (i + 1);
            $column_links = $column_links.append($('<a href="#" data-iwptp-index="' + (i) + '" >' + name.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;') + ' <button type="button" class="iwptp-columns-delete-column" title="Delete Column"><i class="dashicons dashicons-no-alt"></i></button></a>'));
            i++;
        }

        $column_links.append($('<a href="#" data-iwptp-index="add" title="Add column"><i class="dashicons dashicons-plus-alt2"></i></a>'))

        if ($device_columns_heading.children('.iwptp-column-links').length) {
            $device_columns_heading.children('.iwptp-column-links').replaceWith($column_links);

        } else {
            $device_columns_heading.append($column_links);

        }

        // column name input toggle
        var device = $elm.attr('iwptp-model-key'),
            $columns = $elm.children('.iwptp-column-settings').not('[iwptp-disabled]');

        $columns.each(function () {
            var $this = $(this),
                index = $this.siblings('.iwptp-column-settings').addBack().index($this),
                device_icon = { 'laptop': 'square', 'tablet': 'tablet', 'phone': 'smartphone' },
                icon = '<img class="iwptp-column-device-icon iwptp-column-device-icon--' + device + '" src="' + window.iwptp_icons_url + '/' + device_icon[device] + '.svg" />',
                $col_index = $this.find('.iwptp-column-index'),
                $input = $col_index.find('input'),
                col_data = $this.data('iwptp-data'),
                name = col_data.name && col_data.name.trim() ? col_data.name.trim() : '';

            $col_index.find('.iwptp-column-device-icon-container').html(icon).end().children('i').text(index + 1);

            if (name || $input.is(':focus')) {
                $col_index.addClass('iwptp-column-index--input-on');

            } else {
                $col_index.removeClass('iwptp-column-index--input-on');

            }
        })

        if (!e) { // set

        } else { // get

        }

    }

    dominator_ui.controllers.column_settings = function ($elm, data, e) {

        // var device = $elm.parent().attr('iwptp-model-key');

        // // update index marker
        // $elm.siblings().addBack().each(function(){
        // 	var $this = $(this),
        // 			index = $this.siblings('.iwptp-column-settings').addBack().index($this),
        // 			device_icon = {'laptop': 'square', 'tablet': 'tablet', 'phone': 'smartphone'},
        // 			icon = '<img class="iwptp-column-device-icon iwptp-column-device-icon--'+ device +'" src="'+ window.iwptp_icons_url + '/' + device_icon[device] + '.svg" />',
        // 			$col_index = $this.children('.iwptp-column-index'),
        // 			col_data = $this.data('iwptp-data'),
        // 			name = col_data && col_data.name && col_data.name.trim() ? col_data.name.trim() : '';

        // 	$col_index
        // 		.find('.iwptp-device-icon-placeholder').replaceWith( icon )
        // 		.end().children('i').text(index + 1);				

        // 	if( name ){
        // 		$col_index.addClass('iwptp-column-index--input-on');

        // 	}else{
        // 		$col_index.removeClass('iwptp-column-index--input-on');

        // 	}
        // })

        if (!e) { // set


            // $elm.siblings().addBack().each(function(){
            // 	var $this = $(this),
            // 			index = $this.siblings('.iwptp-column-settings').addBack().index($this);
            // 	$this.children('.iwptp-column-index').html( device[0].toUpperCase() + device.substring(1) + ' Column <i>' + ( index + 1 ) + '</i>' );
            // });

            // ensure ids
            if (!window.iwptp_timestamp) {
                window.iwptp_timestamp = Date.now();
            }

            if (!data.heading.id || !data.cell.id) {
                data.heading.id = window.iwptp_timestamp++;
                data.cell.id = window.iwptp_timestamp++;
            }

            // init tabs
            $('.iwptp-tabs', $elm).iwptp_tabs();

            // init block editors
            // -- heading
            $('.iwptp-column-heading-editor', $elm).iwptp_block_editor({
                add_element_partial: 'add-column-heading-element',
                edit_row_partial: false,
                add_row: false,
                connect_with: '.iwptp-column-heading-editor .iwptp-block-editor-row',
                data: data.heading.content,
            });

            // -- cell template
            $('.iwptp-column-template-editor', $elm).iwptp_block_editor({
                add_element_partial: 'add-column-cell-element',
                edit_row_partial: 'cell-row',
                add_row: true,
                connect_with: '.iwptp-column-template-editor .iwptp-block-editor-row',
                data: data.cell.template,
            });

        } else { // get

        }
    }

    dominator_ui.controllers['edit-element-lightbox'] = function ($elm, data, e) {

        if (!e) { // set

            // select icon
            $('.iwptp-select-icon').select2({
                templateResult: function (icon) {
                    if (icon != undefined && icon.text != '') {
                        var img = (icon.id != '') ? '<img class="iwptp-icon-rep" src="' + iwptp_icons_url + '/' + icon.id + '.svg">' : '',
                            $icon = $('<span>' + img + '<span class="iwptp-icon-name">' + icon.text + '</span>' + '</span>');
                        return $icon;
                    }
                },
                templateSelection: function (icon) {
                    if (icon != undefined && icon.text != '') {
                        var img = (icon.id != '') ? '<img class="iwptp-icon-rep" src="' + iwptp_icons_url + '/' + icon.id + '.svg">' : '',
                            $icon = $('<span>' + img + '<span class="iwptp-icon-name">' + icon.text + '</span>' + '</span>');
                        return $icon;
                    }
                },
                dropdownParent: $elm,
            });

            // media image
            if ((data.media_id !== undefined && data.url !== undefined) && (data.media_id !== 'undefined' && data.url !== 'undefined')) {
                var $url = $elm.data('iwptp-children').filter('[iwptp-model-key="url"]'),
                    $img = data.url ? $('<img src="' + data.url + '">') : '',
                    $button = $url.siblings('.iwptp-select-media-button'),
                    mediaUploader = null;
                $url.siblings('.iwptp-selected-media-display').html($img);

                $button.on('click', function (e) {
                    e.preventDefault();
                    var $this = $(this);

                    // If the uploader object has already been created, reopen the dialog
                    if (mediaUploader) {
                        mediaUploader.open();
                        return;
                    }

                    // Extend the wp.media object
                    mediaUploader = wp.media.frames.file_frame = wp.media({
                        title: 'Choose Image',
                        button: {
                            text: 'Choose Image'
                        }, multiple: false
                    });

                    // When a file is selected, grab the URL and set it as the text field's value
                    mediaUploader.on('select', function () {
                        attachment = mediaUploader.state().get('selection').first().toJSON();
                        $this.siblings('[iwptp-model-key="media_id"]').val(attachment.id).change();
                        $this.siblings('[iwptp-model-key="url"]').val(attachment.url).change();
                        $this.siblings('.iwptp-selected-media-display').html('<img style="" src="' + attachment.url + '"/>');
                    });

                    // Open the uploader dialog
                    mediaUploader.open();

                });

            }

            // relabel rules
            if (
                (
                    data.attribute_name &&
                    data.attribute_name !== '_custom' &&
                    (
                        typeof data.relabels === 'undefined' ||
                        typeof window.iwptp_terms_cache === 'undefined' ||
                        typeof window.iwptp_terms_cache[data.attribute_name] === 'undefined'
                    )
                ) ||
                data.taxonomy
            ) {

                // show 'loading...'
                $elm.find('[data-loading="terms"]').show();
                $elm.find('[iwptp-model-key="relabels"]').hide();

                var taxonomy = data.taxonomy ? data.taxonomy : 'pa_' + data.attribute_name,
                    limit_terms;
                if (taxonomy == 'product_cat') {
                    limit_terms = $.extend([], iwptp.data.query.category);
                }

                verify_terms(data.relabels, taxonomy, limit_terms).then(function (modified_terms) {

                    if (modified_terms) {

                        // update data
                        data.relabels = $.extend(true, [], modified_terms);

                        // limit number of terms length
                        data.relabels.length = Math.min(data.relabels.length, 50);

                        // downstream
                        var $relabels = $('[iwptp-model-key="relabels"]', $elm);
                        if ($elm.is('[data-partial="attribute_filter"], [data-partial="category_filter"], [data-partial="taxonomy_filter"]')) {
                            $relabels.html(dominator_ui.row_templates.relabel_rule_term_filter_element_2); // row template
                        } else {
                            $relabels.html(dominator_ui.row_templates.relabel_rule_term_column_element_2); // row template
                        }
                        dominator_ui.init($relabels, data.relabels);

                        // upstream
                        $elm.change();

                    }

                    // hide 'loading...'
                    $elm.find('[data-loading="terms"]').hide();
                    $elm.find('[iwptp-model-key="relabels"]').show();

                })

            }

        } else { // get

            // attribute changed
            if ($(e.target).is('select[iwptp-model-key="attribute_name"], select[iwptp-model-key="taxonomy"]')) {

                // custom attribute - no relabels.. bail
                if (
                    data.attribute_name &&
                    data.attribute_name === '_custom'
                ) {
                    return;
                }

                // select_variation.. bail
                if ($(e.target).closest('[iwptp-row-template="identify_variation"]').length) {
                    return;
                }

                // show 'loading...'
                $elm.find('[data-loading="terms"]').show();
                $elm.find('[iwptp-model-key="relabels"]').hide();

                var taxonomy = data.taxonomy ? data.taxonomy : 'pa_' + data.attribute_name;

                get_terms(taxonomy).then(function (terms) { // async

                    // limit length
                    terms.length = Math.min(terms.length, 50);

                    // update data
                    data.relabels = $.extend(true, [], terms);

                    // downstream
                    var $relabels = $('[iwptp-model-key="relabels"]', $elm);
                    if ($elm.is('[data-partial="attribute_filter"], [data-partial="category_filter"], [data-partial="taxonomy_filter"]')) {
                        $relabels.html(dominator_ui.row_templates.relabel_rule_term_filter_element_2); // row template
                    } else {
                        $relabels.html(dominator_ui.row_templates.relabel_rule_term_column_element_2); // row template
                    }
                    dominator_ui.init($relabels, data.relabels);

                    // upstream
                    $elm.change();

                    // hide 'loading...'
                    $elm.find('[data-loading="terms"]').hide();
                    $elm.find('[iwptp-model-key="relabels"]').show();

                });
            }
        }

        // always 

        // custom field range & price - auto 'single:true'		
        if (
            data.type == 'price_filter' ||
            data.type == 'rating_filter' ||
            (
                data.type == 'custom_field_filter' &&
                data.compare == 'BETWEEN'
            )
        ) {
            if (!data.single) {
                $elm.find('[iwptp-model-key="single"]').prop('checked', true);
                data.single = true;
                $elm.change(); // refresh required to trigger prop conditions
            }
        }
    }

    dominator_ui.controllers.relabels = function ($elm, data, e) {
        if (!e) { // set
            var $tabs = $('.iwptp-tabs', $elm).iwptp_tabs(),
                tabs = $tabs.data('iwptp_tabs');

            // enable clear label tab if clear label field has been set previously
            if (data.clear_label) {
                tabs.ctrl.enable_tab_index(1);
            }

            // remove clear label field if clear label tab is disabled
            $tabs.on('tab_disabled', function (e, index) {
                if (index == 1) {
                    $elm.find('input[iwptp-model-key="clear_label"]').val('').change();
                }
            })

        } else { // get

        }
    }

    dominator_ui.controllers.rating_options = function ($elm, data, e) {
        if (!e) { // set
            var $tabs = $('.iwptp-tabs', $elm).iwptp_tabs(),
                tabs = $tabs.data('iwptp_tabs');

            // enable clear label tab if clear label field has been set previously
            if (data.clear_label) {
                tabs.ctrl.enable_tab_index(1);
            }

            // remove clear label field if clear label tab is disabled
            $tabs.on('tab_disabled', function (e, index) {
                if (index == 1) {
                    $elm.find('input[iwptp-model-key="clear_label"]').val('').change();
                }
            })

        } else { // get

        }
    }

    dominator_ui.controllers.range_options = function ($elm, data, e) {
        if (!e) { // set
            var $tabs = $('.iwptp-tabs', $elm).iwptp_tabs(),
                tabs = $tabs.data('iwptp_tabs');

            // enable clear label tab if clear label field has been set previously
            if (data.clear_label) {
                tabs.ctrl.enable_tab_index(1);
            }

            // remove clear label field if clear label tab is disabled
            $tabs.on('tab_disabled', function (e, index) {
                if (index == 1) {
                    $elm.find('input[iwptp-model-key="clear_label"]').val('').change();
                }
            })

        } else { // get

        }
    }

    dominator_ui.controllers.manual_options = function ($elm, data, e) {
        if (!e) { // set
            $('.iwptp-tabs', $elm).iwptp_tabs();

        } else { // get

        }
    }

    // property list row
    dominator_ui.controllers.property_list_row = function ($elm, data, e) {
        if (!e) { // set
            var $tabs = $('.iwptp-tabs', $elm).iwptp_tabs(),
                tabs = $tabs.data('iwptp_tabs');

        } else { // get

        }
    };

    // archive override row
    dominator_ui.controllers.archive_override_row = function ($elm, data, e) {
        if (!e) { // set
            $('select[multiple]', $elm).select2({
                multiple: true,
                closeOnSelect: false,
                width: '100%'
            });

        } else { // get

        }
    }

    // download csv column heading
    dominator_ui.controllers.download_csv_column = function ($elm, data, e) {
        if (!e) { // set
        } else { // get
            if (!data.column_heading) {
                $('input[iwptp-model-key="column_heading"]', $elm).css({
                    'border-color': '#e53935',
                    'background': 'rgb(244 67 54 / 5%)'
                })
            } else {
                $('input[iwptp-model-key="column_heading"]', $elm).css({
                    'border-color': '',
                    'background': ''
                })
            }

        }
    }

    /* initial data */

    // sku
    dominator_ui.initial_data.element_sku = {
        variable_switch: true,
        style: {},
        condition: {},
    };

    // custom field filter
    dominator_ui.initial_data.element_custom_field_filter = {
        heading: false,
        display_type: 'dropdown',
        manager: '',
        acf_field_type: 'basic',
        manual_options: [],
        range_options: [],
        compare: 'IN',
        show_all_label: 'Show all',
        field_type: 'NUMERIC',
        field_type__exact_match: 'CHAR',
        order__exact_match: 'ASC',
        non_numeric_value_treatement: 'convert',
        ignore_values: 'n.a.| n/a | - | _ | * ',
        empty_value_treatement: 'exclude',
        heading_format__op_selected: 'only_heading',
        search_enabled: false,
        search_placeholder: 'Search',
    };

    //-- manual option
    dominator_ui.initial_data.custom_field_filter_manual_option = {
        'label': '[custom_field_value]',
    };

    //-- range option
    dominator_ui.initial_data.custom_field_filter_range_option = {
        'label': '[custom_field_value]',
    };

    // attribute filter
    dominator_ui.initial_data.element_attribute_filter = {
        display_type: 'dropdown',
        relabels: [],
        heading: [{ "style": {}, "elements": [{ "type": "text", "style": {}, "text": "[attribute] " }], "type": "row" }],
        show_all_label: 'Show all',
        click_action: false,
        heading_format__op_selected: 'only_heading',
        search_enabled: false,
        search_placeholder: 'Search [attribute]',
    };

    //-- relabel
    dominator_ui.initial_data.attribute_filter_relabel_rule = {
        label: '[term_name]',
    };

    // taxonomy filter
    dominator_ui.initial_data.element_taxonomy_filter = {
        display_type: 'dropdown',
        relabels: [],
        heading: [{ "style": {}, "elements": [{ "type": "text", "style": {}, "text": "[taxonomy] " }], "type": "row" }],
        heading_format__op_selected: 'only_heading',
        show_all_label: 'Show all',
        pre_open_depth: 1,
        click_action: false,
        search_enabled: false,
        search_placeholder: 'Search [taxonomy]',
    };

    //-- relabel
    dominator_ui.initial_data.taxonomy_filter_relabel_rule = {
        label: '[term_name]',
    };

    // tags filter
    dominator_ui.initial_data.element_tags_filter = {
        display_type: 'dropdown',
        relabels: [],
        taxonomy: 'product_tag',
        heading: [{ "style": {}, "elements": [{ "type": "text", "style": {}, "text": "Product tags " }], "type": "row" }],
        heading_format__op_selected: 'only_heading',
        show_all_label: 'Show all',
        pre_open_depth: 1,
        click_action: false,
        search_enabled: false,
        search_placeholder: 'Search Tags',
    };

    // //-- relabel
    // dominator_ui.initial_data.tags_filter_relabel_rule = {
    // 	label: '[term_name]',
    // };

    // search
    dominator_ui.initial_data.element_search = {
        heading: '',
        placeholder: 'Search',
        clear_label: 'Search: "[kw]"',
        target: ['title', 'content'],
        custom_fields: [],
        attributes: [],
        keyword_separator: ' ',
        reset_others: true,
    };

    // checkbox
    dominator_ui.initial_data.element_checkbox = {
        heading_enabled: false,
        style: {},
        condition: {},
    };

    // add selected to cart
    dominator_ui.initial_data.element_add_selected_to_cart = {
        add_selected_label: 'Add selected ({total_qty}) for {total_cost}',
        add_selected_label__single_item: '',
        add_selected__unselected_label: 'Add selected items to cart',
        select_all_label: 'Select all',
        clear_all_label: 'Clear all',

        select_all_enabled: true,
        clear_all_enabled: true,
        duplicate_enabled: true,

        style: {
            '[id].iwptp-add-selected--unselected > .iwptp-add-selected__add': {
                opacity: 0.5,
            },
        }
    };

    // pagination
    dominator_ui.initial_data.element_pagination = {
        pagination_type: 'number'
    };

    // mini cart
    dominator_ui.initial_data.element_mini_cart = {
        mini_cart_type: 'default',
        float_position: 'bottom_right',
        side_position: 'right',
        hide_on_zero: 'disable',
        button_text: 'Open Cart',
        title: 'Cart',
        button_size: 100,
        mini_cart_subtotal: 'enable',
        empty_cart_button: 'enable',
        view_checkout_button: 'enable',
        view_cart_button: 'enable',
    };

    // tooltip
    dominator_ui.initial_data.element_tooltip__nav = {
        tooltip_icon: "",
        tooltip_text: "",
        media_id: "",
        url: "",
        trigger: 'hover',
        style: {},
    };

    // tooltip
    dominator_ui.initial_data.element_tooltip = {
        tooltip_icon: "",
        tooltip_text: "",
        media_id: "",
        url: "",
        trigger: 'hover',
        style: {},
    };

    // result count filter
    dominator_ui.initial_data.element_result_count = {
        message: 'Showing [first_result] – [last_result] of [total_results] results',
        single_page_message: 'Showing all [total_results] results',
        single_result_message: 'Showing the single result',
        no_results_message: '',
        style: {}
    };

    // availability filter
    dominator_ui.initial_data.element_availability_filter = {
        display_type: 'dropdown',
        heading: 'In Stock',
        hide_label: 'Hide out of stock',
    };

    // on sale filter
    dominator_ui.initial_data.element_on_sale_filter = {
        display_type: 'dropdown',
        heading: 'On Sale',
        on_sale_label: 'Only on sale items',
    };

    // category filter
    dominator_ui.initial_data.element_category_filter = {
        display_type: 'dropdown',
        heading: 'Category',
        heading_format__op_selected: 'only_heading',
        relabels: [],
        show_all_label: 'Show all',
        taxonomy: 'product_cat',
        hide_empty: false,
        pre_open_depth: 0,
        click_action: false,
        search_enabled: false,
        search_placeholder: 'Search Category',
    };

    // csv download
    dominator_ui.initial_data.element_download_csv = {
        label: 'Download CSV',
        columns: [
            {
                column_heading: 'Name',
                property: 'title',
            },

            {
                column_heading: 'SKU',
                property: 'sku',
            },

            {
                column_heading: 'Regular price',
                property: 'regular_price',
            },

            {
                column_heading: 'Sale price',
                property: 'sale_price',
            },
        ],
    };


    // sort by
    dominator_ui.initial_data.element_sort_by = {
        dropdown_options: [

            // popularity (sales)
            {
                orderby: 'popularity',
                order: 'DESC',
                meta_key: '',
                label: 'Sort by Popularity',
            },

            // rating
            {
                orderby: 'rating',
                order: 'DESC',
                meta_key: '',
                label: 'Sort by Rating',
            },

            // price - ASC
            {
                orderby: 'price',
                order: 'ASC',
                meta_key: '',
                label: 'Sort by Price low to high',
            },

            // price - DESC
            {
                orderby: 'price-desc',
                order: 'DESC',
                meta_key: '',
                label: 'Sort by Price high to low',
            },

            // date
            {
                orderby: 'date',
                order: 'DESC',
                meta_key: '',
                label: 'Sort by Newness',
            },

            // title - ASC
            {
                orderby: 'title',
                order: 'ASC',
                meta_key: '',
                label: 'Sort by Name A - Z',
            },

            // title - DESC
            {
                orderby: 'title',
                order: 'DESC',
                meta_key: '',
                label: 'Sort by Name Z - A',
            },

        ],
    };

    // sort by - option
    dominator_ui.initial_data.sortby_option = {
        orderby: 'price-desc',
        order: 'DESC',
        meta_key: '',
        label: 'Sort by ...',
    };

    // results per page
    dominator_ui.initial_data.element_results_per_page = {
        heading: '[limit] per page',
        dropdown_options: [
            {
                label: '10 per page',
                results: 10,
            },
            {
                label: '20 per page',
                results: 20,
            },
            {
                label: '30 per page',
                results: 30,
            },
        ],
    };

    // results per page - option
    dominator_ui.initial_data.results_per_page_option = {
        results: 10,
        label: '10 per page',
    };

    // category
    dominator_ui.initial_data.element_category = {
        separator: ' ⋅ ',
        empty_relabel: false,
        relabels: [],
        taxonomy: 'product_cat',
        style: {},
        condition: {},
    };

    // attribute
    dominator_ui.initial_data.element_attribute = {
        separator: ' ⋅ ',
        empty_relabel: false,
        relabels: [],
        click_action: '',
        condition: {},
    };

    //-- relabel
    dominator_ui.initial_data.relabels = [];

    // attribute
    dominator_ui.initial_data.element_taxonomy = {
        separator: ' ⋅ ',
        empty_relabel: false,
        relabels: [],
        style: {},
        condition: {},
    };

    // tags
    dominator_ui.initial_data.element_tags = {
        separator: ' ⋅ ',
        empty_relabel: false,
        relabels: [],
        taxonomy: 'product_tag',
        click_action: '',
        style: {},
        condition: {},
    };

    // custom_field
    dominator_ui.initial_data.element_custom_field = {
        default_relabel: '[custom_field_value]',
        empty_relabel: '',
        relabel_rules: [],
        manager: '',
        media_img_size: 'thumbnail',
        img_val_type: 'url',
        display_as: 'text',
        pdf_link_label: 'Download',
        pdf_val_type: 'url',
        style: {},
        condition: {},
    };

    //-- relabel
    dominator_ui.initial_data.custom_field_relabel_rule = {
        label: '[custom_field_value]',
        compare: '=',
    };

    // dot
    dominator_ui.initial_data.element_dot = {
        style: {},
    };

    // dot__col
    dominator_ui.initial_data.element_dot__col = {
        style: {},
        condition: {},
    };

    // space
    dominator_ui.initial_data.element_space = {
        width: '',
        style: {},
    };

    // space__col
    dominator_ui.initial_data.element_space__col = {
        width: '',
        style: {},
        condition: {},
    };

    // title
    dominator_ui.initial_data.element_title = {
        product_link_enabled: true,
        style: {},
        condition: {},
    };

    // content
    dominator_ui.initial_data.element_content = {
        limit: '',
        toggle_enabled: false,
        show_more_label: 'Show more (+)',
        show_less_label: 'Show less (-)',
        read_more_label: '',
        truncation_symbol: '',
        shortcode_action: '',
        style: {},
        condition: {},
    };

    // excerpt
    dominator_ui.initial_data.element_excerpt = {
        limit: '',
        toggle_enabled: false,
        show_more_label: 'Show more (+)',
        show_less_label: 'Show less (-)',
        read_more_label: '',
        truncation_symbol: '',
        style: {},
        condition: {},
    };

    // short description
    dominator_ui.initial_data.element_short_description = {
        limit: '',
        toggle_enabled: false,
        show_more_label: 'Show more (+)',
        show_less_label: 'Show less (-)',
        read_more_label: '',
        truncation_symbol: '',
        style: {},
        condition: {},
    };

    // total
    dominator_ui.initial_data.element_total = {
        output_template: '{n}',
        no_output_template: '',
        variable_switch: true,
        style: {},
        condition: {},
    };

    // text
    dominator_ui.initial_data.element_text = {
        text: '',
        style: {},
    };

    // text__col
    dominator_ui.initial_data.element_text__col = {
        text: '',
        style: {},
        condition: {},
    };

    // html
    dominator_ui.initial_data.element_html = {
        html: '',
        style: {},
    };

    // html__col
    dominator_ui.initial_data.element_html__col = {
        html: '',
        style: {},
        condition: {},
    };

    // select__variation
    dominator_ui.initial_data.element_select__variation = {
        type: 'radio',
        style: {},
        condition: {},
    };

    // select__variation
    dominator_ui.initial_data.element_select__variation = {
        type: 'radio',
        style: {},
        condition: {},
    };

    // price__variation
    dominator_ui.initial_data.element_price__variation = {
        style: {},
        condition: {},
    };

    // price__variation
    dominator_ui.initial_data.element_quantity = {
        condition: {},
        display_type: 'input',
        controls: 'browser',
        qty_label: 'Qty: ',
        max_qty: 10,
        initial_value: 'min',
        qty_warning: 'Max: [max]',
        min_qty_warning: 'Min: [min]',
        qty_step_warning: 'Step: [step]',
        return_to_initial: true,
        style: {},
        condition: {},
    };

    // property list
    dominator_ui.initial_data.element_property_list = {
        show_more_label: 'Show more',
        show_less_label: 'Show less',
        rows: [{
            "property_name": [{ "style": {}, "elements": [], "type": "row" }],
            "property_value": [{ "style": {}, "elements": [], "type": "row" }],
            "condition": { "action": "show", "product_type": [] },
        }],
        initial_reveal: 4,
        columns: 1,
        style: {},
        condition: {},
    };

    // property list row
    dominator_ui.initial_data.property_list_row = {
        property_name: false,
        property_value: false,
        condition: {
            action: 'show',
        },
        style: {},
    };

    // dimensions
    dominator_ui.initial_data.element_dimensions = {
        variable_switch: true,
        condition: {},
        style: {},
    };

    // on sale
    dominator_ui.initial_data.element_on_sale = {
        "template": [{ "style": [], "elements": [{ "type": "text", "style": { "[id]": {} }, "text": "-[percent_diff]%" }], "type": "row" }],
        "style": { "[id]": {} },
        "variable_switch": false,
        "condition": {}
    };

    // availability
    dominator_ui.initial_data.element_availability = {
        out_of_stock_message: 'Out of stock',
        single_stock_message: 'Only 1 left',
        on_backorder_message: 'On backorder',
        on_backorder_managed_message: '[stock] left (can backorder)',
        low_stock_threshold: 3,
        low_stock_message: 'Only [stock] left',
        in_stock_message: 'In stock',
        in_stock_managed_message: '[stock] in stock',
        variable_switch: true,
        style: {},
        condition: {},
    };

    // stock
    dominator_ui.initial_data.element_stock = {
        range_labels: '',
        style: {},
        condition: {},
        variable_switch: true,
    };

    // product_id
    dominator_ui.initial_data.element_product_id = {
        variable_switch: true,
        style: {},
        condition: {},
    };

    // price
    dominator_ui.initial_data.element_price = {
        "sale_template": [{
            "style": {}, "elements": [
                { "type": "sale_price", "style": { ".iwptp-product-on-sale [id]": {} } },
                { "type": "regular_price", "style": { ".iwptp-product-on-sale [id]": {} } }
            ]
        }],

        "template": [{
            "style": {}, "elements": [
                { "type": "regular_price", "style": { ".iwptp-product-on-sale [id]": {} } }
            ]
        }],

        "variable_template": [{
            "style": [], "elements": [
                { "type": "lowest_price", "style": { "[id]": {} } },
                { "type": "text", "style": { "[id]": { "margin-right": "6px" } }, "text": "-" },
                { "type": "highest_price", "style": { "[id]": {} } }
            ]
        }], "style": { "[id]": {} }, "type": "price",

        style: {},
        condition: {},
    };

    // product image
    dominator_ui.initial_data.element_product_image = {
        size: 'thumbnail',
        placeholder_enabled: true,
        click_action: 'product_page',
        zoom_trigger: '',
        zoom_scale: '2.0',
        custom_zoom_scale: '1.75',
        icon_when: 'always',
        style: {
            '[id]': {},
            '[id] > .iwptp-lightbox-icon': {},
        },
        condition: {},
    };

    // gallery
    dominator_ui.initial_data.element_gallery = {
        max_images: 3,
        see_more_label: '+{n} more',
        include_featured: false,
        style: {},
        condition: {},
    };

    // featured
    dominator_ui.initial_data.element_featured = {
        icon: '',
        text: 'Featured',
        style: {},
    };

    // icon
    dominator_ui.initial_data.element_icon = {
        name: 'chevron-right',
        style: {},
    };

    // icon__col
    dominator_ui.initial_data.element_icon__col = {
        name: 'chevron-right',
        style: {},
        condition: {},
    };

    // button
    dominator_ui.initial_data.element_button = {
        label: 'Buy here',
        target: '_self',
        custom_field: '',
        link: 'product_link',
        custom_field_empty_relabel: false,
        use_default_template: false,
        condition: {},
    };

    // select variation
    dominator_ui.initial_data.element_select_variation = {
        display_type: 'dropdown',

        // naming rules
        hide_attributes: true,
        attribute_term_separator: ': ',
        attribute_separator: ', ',

        //radio_single
        variation_name: '',
        template: [{ "style": {}, "elements": [{ "type": "select__variation", "style": {}, "condition": [], "html_class": "" }, { "text": "[variation_name]: ", "style": {}, "condition": [], "type": "text" }, { "style": {}, "condition": [], "type": "price__variation", "html_class": "" }], "type": "row" }],
        attribute_terms: [{ 'taxonomy': '', 'term': '' }],
        not_exist_template: false,
        out_of_stock_template: false,
        non_variable_template: false,

        style: {
            '[id] > .iwptp-select-variation-dropdown': {},
            '[id].iwptp-select-variation-radio-multiple-wrapper': {},
        },
        condition: {},
    };

    // cart form
    dominator_ui.initial_data.element_cart_form = {
        visible_elements: [
            'quantity',
            'button',
            'availability',
            'variation_description',
            'variation_price',
            'variation_attributes',
        ],
        style: {},
        condition: {},
    };

    // shortcode
    dominator_ui.initial_data.element_shortcode = {
        shortcode: '',
        style: {},
        condition: {},
    };

    // product link
    dominator_ui.initial_data.element_product_link = {
        suffix: '',
        template: 'View details',
        target: '_self',
        condition: {},
        style: {},
    };

    // date
    dominator_ui.initial_data.element_date = {
        format: '',
        condition: {},
        style: {},
    };

    // apply / reset
    dominator_ui.initial_data.element_apply_reset = {
        apply_label: 'Apply',
        reset_label: 'Reset',
        style: {},
    };

    // rating
    dominator_ui.initial_data.element_rating = {
        "template": [{ "style": [], "elements": [{ "type": "rating_number", "style": { "[id]": {} }, "trim_decimal": true, "decimals": true, "dec_point": "." }, { "type": "rating_stars", "style": { "[id]": {}, "[id] i:after": {}, "[id] i:before": {} } }, { "type": "review_count", "style": {}, "brackets": true }], "type": "row" }], "type": "rating", "style": { "[id]": {} },
        "not_rated": '',
        "rating_source": 'woocommerce',
        "condition": {},
    };

    // sorting
    dominator_ui.initial_data.element_sorting = {
        orderby: 'title',
        meta_key: '',
    };

    // media_image
    dominator_ui.initial_data.element_media_image = {
        url: '',
        media_id: '',
        use_external_source: false,
        external_source: '',
    };

    // full_screen
    dominator_ui.initial_data.element_full_screen = {
        url: '',
        media_id: '',
    };

    // media_image__col
    dominator_ui.initial_data.element_media_image__col = {
        url: '',
        media_id: '',
        use_external_source: false,
        external_source: '',
        condition: {},
        style: {},
    };

    // Columns
    dominator_ui.initial_data.column_settings = {
        name: '',
        heading: {
            content: null,
            style: {}
        },
        cell: {
            template: null,
            style: {}
        }
    };

    dominator_ui.initial_data.columns = {
        laptop: [
            dominator_ui.initial_data.column_settings
        ],
        tablet: [

        ],
        phone: [

        ],

    };

    // Navigation

    // conditions
    dominator_ui.panel_conditions.cf_show_all_label = function ($elm, data) { // parent $elm

        if (
            // if 'exact match' comparison is selected && single option enabled
            (data.compare == 'IN' && data.single) ||
            // or if 'within range' comparison match is selected
            (data.compare == 'BETWEEN')
        ) {
            return true;
        } else {
            return false;
        }
    };

    // nav controller
    dominator_ui.controllers.nav_header_row = function ($elm, data, e) {
        if (typeof data.ratio == 'undefined') {
            data.ratio = '100-0';
        }

        $elm.removeClass('iwptp-ratio-100-0 iwptp-ratio-70-30 iwptp-ratio-50-50 iwptp-ratio-30-70 iwptp-ratio-0-100');
        $elm.addClass('iwptp-ratio-' + data.ratio);

        if (!e) { // set

        } else { // get

        }
    }

    dominator_ui.controllers.nav_footer_row = function ($elm, data, e) {
        if (typeof data.ratio == 'undefined') {
            data.ratio = '100-0';
        }

        $elm.removeClass('iwptp-ratio-100-0 iwptp-ratio-70-30 iwptp-ratio-50-50 iwptp-ratio-30-70 iwptp-ratio-0-100');
        $elm.addClass('iwptp-ratio-' + data.ratio);

        if (!e) { // set

        } else { // get

        }
    }

    // nav initial data
    dominator_ui.initial_data.nav_header_row = {
        columns_enabled: 'left-right',
        ratio: '100-0',
        columns: {
            left: { template: false },
            center: { template: false },
            right: { template: false },
        }
    };

    dominator_ui.initial_data.nav_footer_row = {
        columns_enabled: 'left-right',
        ratio: '100-0',
        columns: {
            left: { template: false },
            center: { template: false },
            right: { template: false },
        }
    };

    dominator_ui.initial_data.navigation = {
        laptop: {
            header: {
                rows: [
                    dominator_ui.initial_data.nav_header_row,
                ],
            },
            footer: {
                rows: [
                    dominator_ui.initial_data.nav_footer_row,
                ],
            },
            left_sidebar: [],
        },
        tablet: false,
        phone: false,
    };

    dominator_ui.initial_data.element_price_filter = {
        heading: 'Price range',
        heading_format__op_selected: 'only_heading',
        style: {},
        show_all_label: 'Any price',
        single: true,
        range_options: [
            {
                label: 'Upto $50',
                min: '0',
                max: '50',
            },
            {
                label: '$51 - $100',
                min: '51',
                max: '100',
            },
            {
                label: 'Over $100',
                min: '100',
                max: '',
            },
        ]
    };

    dominator_ui.initial_data.price_range_row_2 = {
        label: '$100 - $200',
        min: '100',
        max: '200',
    };

    dominator_ui.initial_data.element_rating_filter = {
        heading: 'Rating',
        heading_format__op_selected: 'only_heading',
        style: {},
        show_all_label: 'Show all',
        rating_options: [
            {
                label: '',
                value: '5',
                enabled: false,
            },
            {
                label: '& Up',
                value: '4+',
                enabled: true,
            },
            {
                label: '& Up',
                value: '3+',
                enabled: true,
            },
            {
                label: '& Up',
                value: '2+',
                enabled: true,
            },
            {
                label: '& Up',
                value: '1+',
                enabled: true,
            },
        ]
    };

    dominator_ui.initial_data.rating_filter_row = {
        label: '& Up',
        value: '1+',
    };

    dominator_ui.initial_data.element_filter_modal = {
        label: [
            {
                "style": {},
                "elements":
                    [
                        { "type": "icon", "style": {}, "name": "filter" },
                        { "type": "text", "style": {}, "text": "Filter" }
                    ],
                "type": "row"
            }
        ],
        style: {},
    };

    dominator_ui.initial_data.element_sort_modal = {
        label: [
            {
                "style": {},
                "elements":
                    [
                        { "type": "icon", "style": {}, "name": "bar-chart" },
                        { "type": "text", "style": {}, "text": "Sort" }
                    ],
                "type": "row"
            }
        ],
        style: {},
    };

    dominator_ui.initial_data.archive_override_rule = {
        category: [],
        attribute: [],
        tag: [],
        table_id: '',
    };

    // button
    dominator_ui.initial_data.element_clear_filters = {
        reset_label: 'Clear filters',
    };

    function get_terms(taxonomy, limit_terms) {
        return new Promise((resolve, reject) => {
            var terms = terms_cache(taxonomy);
            if (!terms) {
                return get_terms_from_server(taxonomy, limit_terms).then(function (terms) {
                    terms_cache(taxonomy, terms);
                    resolve(terms);
                });
            } else {
                resolve(terms);
                return terms;
            }
        })
    }

    function terms_cache(taxonomy, terms) {
        if (!window.iwptp_terms_cache) {
            window.iwptp_terms_cache = {};
        }
        if (terms) { // set
            window.iwptp_terms_cache[taxonomy] = terms;
            return;
        } else { // get
            if (typeof window.iwptp_terms_cache[taxonomy] == 'undefined') {
                return false;
            }
            return window.iwptp_terms_cache[taxonomy]
        }
    }

    function get_terms_from_server(taxonomy, limit_terms) {
        return new Promise((resolve, reject) => {
            var ajax_data = {
                action: 'iwptp_get_terms',
                nonce: IWPTP_DATA.ajax_nonce,
                taxonomy: taxonomy,
                limit_terms: limit_terms,
            },
                terms = [];

            $.post(ajaxurl, ajax_data, function (terms) {
                resolve(terms);
                return terms;
            });
        })
    }

    function verify_terms(current_terms, taxonomy, limit_terms) {
        return new Promise((resolve, reject) => {
            get_terms(taxonomy, limit_terms).then(function (terms) {
                var modified = false;

                // ensure all terms are included in current terms
                for (var i = 0; i < terms.length; i++) {
                    var included = false;
                    for (var ii = 0; ii < current_terms.length; ii++) {
                        if (current_terms[ii].term == terms[i].term) {
                            included = true;
                        }
                    }

                    if (!included) {
                        current_terms.push($.extend({}, terms[i]));
                        modified = true;
                    }
                }

                // ensure no current terms are from outside of terms
                var remove = []
                for (var i = 0; i < current_terms.length; i++) {
                    var included = false;
                    for (var ii = 0; ii < terms.length; ii++) {
                        if (current_terms[i].term == terms[ii].term) {
                            included = true;
                        }
                    }

                    if (!included) {
                        remove.push(i);
                        modified = true;
                    }
                }

                if (remove.length) {
                    for (var i = 0; i < remove.length; i++) {
                        current_terms.splice(remove[i], 1);
                    }
                }

                if (modified) {
                    resolve(current_terms);
                } else {
                    resolve(false);
                }

                return terms;
            })
        })
    }

})