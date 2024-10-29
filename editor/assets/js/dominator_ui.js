var dominator_ui = {};

jQuery(function ($) {
    dominator_ui = {

        init: function ($elm, data) {

            if (typeof $elm == 'string') {
                $elm = $($elm);
            }

            var _ = this,
                $controllers = $elm.find('[iwptp-controller]').addBack(),
                $data_nodes = $elm.find('[iwptp-model-key]').addBack(),
                $value_forwards = $elm.find('[iwptp-value-forward]'),
                $content_templates = $elm.find('[iwptp-content-template]'),
                $row_wrapper = $elm.find('[iwptp-row-template]').parent(),
                $add_row = $elm.find('[iwptp-add-row-template]'),
                $duplicate_row = $elm.find('[iwptp-duplicate-row]'),
                $move_row_up = $elm.find('[iwptp-move-up]'),
                $move_row_down = $elm.find('[iwptp-move-down]'),
                $remove_row = $elm.find('[iwptp-remove-row]'),
                $panel_conditions = $elm.find('[iwptp-panel-condition]');

            // initial data
            if ($elm.attr('iwptp-initial-data')) {
                $.each(data, function (key, val) { // #purpose
                    if (
                        val !== null &&
                        typeof val === 'object' &&
                        val.constructor.toString().indexOf("Array") == -1 &&
                        jQuery.isEmptyObject(val)
                    ) {
                        delete data[key];
                    }
                });

                var initial_data = $.extend({}, _.initial_data[$elm.attr('iwptp-initial-data')]);

                $.each(initial_data, function (key, val) {
                    if (data != null && typeof data[key] !== 'undefined') {
                        delete initial_data[key];
                    }
                })

                var data = $.extend(true, {}, initial_data, data);
            }

            // register relations
            $controllers.add($data_nodes).each(function () {
                var $child = $(this),
                    $parent = $child.parent().closest('[iwptp-model-key]'),
                    $children = $parent.data('iwptp-children');
                if (!$children) {
                    $children = $();
                }
                $children = $children.add($child);
                $parent.data('iwptp-children', $children);
                $child.data('iwptp-parent', $parent);
            })

            $value_forwards.each(function () {
                var $this = $(this),
                    selector = $this.attr('iwptp-value-forward'),
                    $data_node = $this.closest(selector);
                $this.data('iwptp-data-node', $data_node);
                $data_node.data('iwptp-value-forward', $this);
            })

            $content_templates.each(function () {
                var $this = $(this),
                    $data_node = $this.closest('[iwptp-model-key]');

                $this.data('iwptp-data-node', $data_node);

                var $content_templates = $data_node.data('iwptp-content-templates');
                if (!$content_templates) {
                    $content_templates = $();
                }
                $content_templates = $content_templates.add($this);
                $data_node.data('iwptp-content-templates', $content_templates);
            })

            $panel_conditions.each(function () {
                var $this = $(this),
                    ancestor_skip = $this.attr('iwptp-panel-ancestor-skip'),
                    $data_node = $this.parent().closest('[iwptp-model-key]');

                if (!ancestor_skip) {
                    ancestor_skip = 0;
                }

                while (ancestor_skip > 0) {
                    $data_node = $data_node.parent().closest('[iwptp-model-key]');

                    --ancestor_skip;
                }

                $this.data('iwptp-parent', $data_node);

                var $panel_conditions = $data_node.data('iwptp-panel-conditions');
                if (!$panel_conditions) {
                    $panel_conditions = $();
                }
                $panel_conditions = $panel_conditions.add($this);
                $data_node.data('iwptp-panel-conditions', $panel_conditions);
            })

            // row sorting
            $row_wrapper.each(function () {
                var $wrapper = $(this),
                    connect_with = $wrapper.attr('iwptp-connect-with');
                if ($wrapper.hasClass('iwptp-sortable')) {
                    $wrapper.sortable({
                        handle: function () { return $wrapper.find('.iwptp-sortable-handle').first(); },
                        connectWith: connect_with,
                        items: '>[iwptp-model-key="[]"]',
                        update: function (e, ui) {
                            // ensure 'add row' button remains beneath the rows
                            var $button = ui.item.siblings('.iwptp-button[iwptp-add-row-template]'),
                                $last_row = ui.item.siblings().addBack().filter('[iwptp-row-template].iwptp-last-row')

                            if ($button.length && $last_row.length && ($button.index() < $last_row.index())) {
                                $button.detach().insertAfter($last_row);
                            }

                        }
                    })
                }
            })

            // row controllers
            $add_row.add($duplicate_row).add($remove_row).add($move_row_up).add($move_row_down).each(function () {
                var $this = $(this),
                    $row = $this.closest('[iwptp-model-key]'),
                    $rows_wrapper = $row.data('iwptp-parent'),
                    rows_data = $rows_wrapper.data('iwptp-data');
                $this.data('iwptp-parent', $row);
            })

            $remove_row.each(function () {
                var $this = $(this),
                    $row = $this.data('iwptp-parent'),
                    min_rows = $row.attr('iwptp-min-rows') ? parseInt($row.attr('iwptp-min-rows')) : 0,
                    $rows_wrapper = $row.data('iwptp-parent'),
                    rows_data = $rows_wrapper.data('iwptp-data');

                if (min_rows && typeof rows_data != 'undefined' && Object.prototype.toString.call(rows_data) == '[object Array]') {
                    if (rows_data.length > min_rows) {
                        $this.removeAttr('iwptp-disabled');
                    } else {
                        $this.attr('iwptp-disabled', '');
                    }
                }
            })

            // register row templates
            $elm.find('[iwptp-row-template]').each(function () {
                var $template = $(this),
                    template_name = $template.attr('iwptp-row-template');
                if (!_.row_templates[template_name]) {
                    _.row_templates[template_name] = $template[0].outerHTML;
                    $template.attr('iwptp-disabled', '');
                }
            })

            // radio buttons 'name' attribute
            $controllers.add($data_nodes).each(function () {
                var $this = $(this),
                    $children = $this.data('iwptp-children'),
                    fixed = [];

                if (!$children || !$children.length) {
                    return;
                }

                $children.filter('[type="radio"]').each(function () {
                    var $radio = $(this),
                        model_key = $radio.attr('iwptp-model-key');

                    if (-1 !== $.inArray(model_key, fixed)) {
                        return;
                    }

                    fixed.push(model_key);

                    var name = 'iwptp-' + model_key + '-' + Math.random(0, 1.0e+9);

                    $children.filter('[type="radio"][iwptp-model-key="' + model_key + '"]').each(function () {
                        var $sibling = $(this);
                        $sibling.attr('name', name);
                    })
                })

            })

            // bind change handler
            $controllers.add($data_nodes).off('change keyup').on('change keyup', function (e) {

                var $this = $(this),
                    key = $this.attr('iwptp-model-key'),
                    index = $this.attr('iwptp-model-key-index'),
                    $parent = $this.data('iwptp-parent'),
                    val;

                if (-1 !== $.inArray($this[0].tagName, ['SELECT', 'INPUT', 'TEXTAREA'])) {
                    if (-1 !== $.inArray($this[0].type, ['checkbox'])) {
                        val = $this[0].checked;
                    } else {
                        val = $this.val();
                    }
                } else {
                    val = $this.data('iwptp-data');
                }

                if ($parent && $parent.length) {
                    var parent_data = $parent.data('iwptp-data');
                    // feed data to parent
                    switch (key) {
                        // array
                        case '[]':
                            if (!parent_data) {
                                parent_data = [];
                            }
                            parent_data[index] = val;
                            break;
                        // object key
                        default:

                            if (key.length > 2 && key.slice(-2) == '[]') {
                                // input:checkbox[]

                                var true_key = key.slice(0, -2),
                                    val = $this.val();
                                if (typeof parent_data[true_key] == 'undefined' || !Array.isArray(parent_data[true_key])) {
                                    parent_data[true_key] = [];
                                }

                                if ($this.prop('checked')) {
                                    if (-1 === parent_data[true_key].indexOf(val)) {
                                        parent_data[true_key].push(val);
                                    }

                                } else {
                                    var index = parent_data[true_key].indexOf(val);
                                    if (index > -1) {
                                        parent_data[true_key].splice(index, 1);
                                    }

                                }

                            } else {
                                // other input

                                if (!parent_data || Array.isArray(parent_data)) {
                                    parent_data = {};

                                }
                                parent_data[key] = val;

                            }
                    }
                    $parent.data('iwptp-data', parent_data);
                }

                var $content_templates = $this.data('iwptp-content-templates');
                if ($content_templates && typeof val == 'object') {
                    $content_templates.each(function () {
                        var $content_template = $(this),
                            _key = $content_template.attr('iwptp-content-template'),
                            _val = val.hasOwnProperty(_key) ? val[_key] : '',
                            value_modifier = $content_template.attr('iwptp-value-modifier');
                        if (typeof _.value_modifiers[value_modifier] == 'function') {
                            _val = _.value_modifiers[value_modifier](_val);
                        }
                        $content_template.text(_val);
                    })
                }

                // panel conditions
                var $panel_conditions = $this.data('iwptp-panel-conditions');
                if ($panel_conditions) {
                    var condition_context = $this.data('iwptp-data');
                    $panel_conditions.each(function () {
                        var $this = $(this),
                            condition_key = $this.attr('iwptp-panel-condition');

                        if (_.panel_conditions[condition_key]($this, condition_context)) {
                            $this.removeAttr('iwptp-disabled');
                            var enable_handler = $this.attr('iwptp-panel-enable');
                            if (enable_handler) {
                                _.panel_enable[enable_handler]($this);
                            }

                        } else {
                            $this.attr('iwptp-disabled', '');
                            var disable_handler = $this.attr('iwptp-panel-disable');
                            if (disable_handler) {
                                _.panel_disable[disable_handler]($this);
                            }

                        }
                    })
                }

                // [iwptp-model-key] siblings must match value
                var $parent = $this.data('iwptp-parent');
                if ($parent && $parent.length && key && key !== '[]' && key.slice(-2) !== '[]') { // not template nor checkbox/radio
                    var parent_data = $parent.data('iwptp-data'),
                        $siblings = $parent.data('iwptp-children');

                    // var $twins      = $siblings.filter('[iwptp-model-key="'+ key +'"]').not($this);
                    // if( $twins.length ){
                    //   _.set_data($parent, parent_data);
                    // }
                }

                if ($this.attr('iwptp-controller')) {
                    var controller_name = $this.attr('iwptp-controller');
                    if (typeof _.controllers[controller_name] !== 'undefined') {
                        _.controllers[controller_name]($this, val, e);
                    }
                }

            })

            // bind sortupdate handler
            $controllers.add($data_nodes).off('sortupdate').on('sortupdate', function (e) {
                var $wrapper = $(this),
                    $target = $(e.target);
                if ($wrapper.is($target) && typeof $wrapper.data('iwptp-children') !== 'undefined') {
                    _.reindex_rows($wrapper);
                }
            })

            // bind sortreceive handler
            $controllers.add($data_nodes).off('sortreceive').on('sortreceive', function (e, ui) {
                var $wrapper = $(this),
                    $target = $(e.target);

                if ($wrapper.is($target) && typeof $wrapper.data('iwptp-children') !== 'undefined') {
                    var $item = ui.item,
                        wrapper_data = $wrapper.data('iwptp-data'),
                        item_data = $item.data('iwptp-data'),
                        index = $item.index();

                    _.init($item, item_data);
                    wrapper_data.splice(index, 0, item_data);
                    _.reindex_rows($wrapper);
                }
            })

            // forward values to a data node above
            $value_forwards.off('change').on('change', function () {
                var $this = $(this),
                    val = $this.val();
                $data_node = $this.data('iwptp-data-node');
                $data_node.data('iwptp-data', val);
            })

            // add another row
            $add_row.off('click').on('click', function () {
                var $button;
                var buttonIsMiddle = false;
                var nextTemplate;

                if ($(this).hasClass('iwptp-columns-add-row-without-animation') === true) {
                    $button = $(this).closest('.iwptp-editor-columns-container').find('button.iwptp-columns-add-row-button-main');
                } else if ($(this).hasClass('iwptp-middle-cols-button') === true) {
                    buttonIsMiddle = true;
                    nextTemplate = $(this).closest('.iwptp-column-settings');
                    $button = $(this).closest('.iwptp-editor-columns-container').find('button.iwptp-columns-add-row-button-main');
                } else {
                    $button = $(this);
                }

                var container = $(this).closest('.iwptp-editor-columns-container'),
                    template_name = $button.attr('iwptp-add-row-template'),
                    direction = $button.attr('iwptp-direction'),
                    $template = $(_.row_templates[template_name]),
                    $last_row = $button.prev('[iwptp-row-template]'),
                    index = 0;

                if ($last_row.length && $last_row.attr('iwptp-row-template') == template_name && $last_row.attr('iwptp-model-key-index')) {
                    var index = parseInt($last_row.attr('iwptp-model-key-index')) + 1;
                }

                if (buttonIsMiddle === true && nextTemplate.length > 0) {
                    $template.insertBefore(nextTemplate);
                } else {
                    if (!direction || direction == 'before') {
                        $template.insertBefore($button);
                    } else {
                        $template.insertAfter($button);
                    }
                }

                _.init($template, {});

                var $rows_wrapper = $button.data('iwptp-parent');
                _.reindex_rows($rows_wrapper);

                if (!$(this).hasClass('iwptp-columns-add-row-without-animation')) {
                    window.iwptp_feedback_anim('add_new_row', $template);
                }

                setTimeout(function () {
                    iwptpColumnsRowTopFix(container);
                }, 150)
            })

            // duplicate row
            $duplicate_row.off('click').on('click', function () {
                var $this = $(this),
                    $row = $this.data('iwptp-parent'),
                    container = $(this).closest('.iwptp-editor-columns-container'),
                    row_data = $row.data('iwptp-data'),
                    template_name = $row.attr('iwptp-row-template'),
                    $template = $(_.row_templates[template_name]),
                    index = parseInt($row.attr('iwptp-model-key-index')) + 1;

                $template.insertAfter($row);
                var copy_row_data = {};
                $.extend(true, copy_row_data, row_data);
                _.init($template, _.refresh_ids(copy_row_data));

                var $rows_wrapper = $row.data('iwptp-parent');
                _.reindex_rows($rows_wrapper);

                if ($row.hasClass('iwptp-toggle-column-expand')) {
                    $template.addClass('iwptp-toggle-column-expand');
                } else {
                    $template.removeClass('iwptp-toggle-column-expand');
                }

                window.iwptp_feedback_anim('duplicate_row', $template);

                setTimeout(function () {
                    iwptpColumnsRowTopFix(container);
                }, 150)
            })

            // remove row
            $remove_row.off('click').on('click', function () {
                var $this = $(this),
                    heading = $this.closest('.iwptp-editor-columns-container').find('.iwptp-editor-light-heading-fixed'),
                    container = $(this).closest('.iwptp-editor-columns-container'),
                    $row = $this.data('iwptp-parent'),
                    $sibling_rows = $row.siblings('[iwptp-row-template]'),
                    index = $row.attr('iwptp-model-key-index');
                var $rows_wrapper = $row.data('iwptp-parent');

                swal({
                    title: "Are you sure?",
                    type: "warning",
                    showCancelButton: true,
                    cancelButtonClass: "iwptp-button iwptp-button-lg iwptp-button-white",
                    confirmButtonClass: "iwptp-button iwptp-button-lg iwptp-button-blue",
                    confirmButtonText: "Yes, i'm sure",
                    closeOnConfirm: true
                }, function (isConfirm) {
                    if (isConfirm) {
                        window.iwptp_feedback_anim('delete_row', $row);
                        $row.remove();
                        _.reindex_rows($rows_wrapper);

                        setTimeout(function () {
                            iwptpColumnsRowTopFix(container);
                        }, 150)
                    }
                });
            })

            // move row up
            $move_row_up.off('click').on('click', function () {
                var $this = $(this),
                    $row = $this.data('iwptp-parent'),
                    $prev = $row.prev('[iwptp-row-template]'),
                    $rows_wrapper = $row.data('iwptp-parent');

                if ($prev.length) {
                    $row.insertBefore($prev)
                    _.reindex_rows($rows_wrapper);
                    iwptp_feedback_anim('move_row_up', $row);
                }
            })

            // move row down
            $move_row_down.off('click').on('click', function () {
                var $this = $(this),
                    $row = $this.data('iwptp-parent'),
                    $next = $row.next('[iwptp-row-template]'),
                    $rows_wrapper = $row.data('iwptp-parent');

                if ($next.length) {
                    $row.insertAfter($next)
                    _.reindex_rows($rows_wrapper);
                    iwptp_feedback_anim('move_row_down', $row);
                }
            })

            // set values for children
            if (data) {
                _.set_data($elm, data);
            }

        },

        set_data: function ($elm, data) {
            $elm.data('iwptp-data', data);

            if (typeof $elm.attr('iwptp-block-editor') !== 'undefined') {
                $elm.iwptp_block_editor();
                return;
            }

            var _ = this,
                $children = $elm.data('iwptp-children');

            if ($children) {
                $children.each(function () {
                    var $this = $(this),
                        key = $this.attr('iwptp-model-key');

                    switch (key) {
                        // rows array
                        case '[]':
                            var template_name = $this.attr('iwptp-row-template'),
                                $template = $(_.row_templates[template_name]);

                            $this.siblings('[iwptp-row-template="' + template_name + '"]').remove();
                            $this.replaceWith($template);
                            $this = $template;

                            if (typeof data[0] == 'undefined') {
                                $this.remove();
                                break;
                            }

                            _.init($this, data[0]);
                            $this.addClass('iwptp-first-row iwptp-last-row');

                            if (data.length > 1) {
                                $this.removeClass('iwptp-last-row');

                                var $current = $template,
                                    i = 1;

                                while (i < data.length) {
                                    var $new = $(_.row_templates[template_name]);
                                    $new.attr('iwptp-model-key-index', i);
                                    $current.after($new);
                                    $current = $new;
                                    _.init($current, data[i]);
                                    ++i;
                                }
                                $current.addClass('iwptp-last-row')
                            }
                            break;

                        // object [key]
                        default:

                            if (key.length > 2 && key.slice(-2) == '[]') {
                                // input:checkbox[] / radio[]

                                var true_key = key.slice(0, -2),
                                    val = $this.val();
                                if (typeof data[true_key] == 'undefined' || !Array.isArray(data[true_key])) {
                                    data[true_key] = [];
                                }

                                $this.prop('checked', (data[true_key].indexOf(val) > -1));
                                if ($this.attr('iwptp-controller')) {
                                    var controller_name = $this.attr('iwptp-controller');
                                    if (typeof _.controllers[controller_name] !== 'undefined') {
                                        _.controllers[controller_name]($this);
                                    }
                                }

                            } else {
                                // other input

                                if (typeof data[key] !== 'undefined') {
                                    if (-1 !== $.inArray($this[0].tagName, ['INPUT', 'SELECT', 'TEXTAREA'])) {
                                        if ($this[0].type == 'checkbox') {
                                            data[key] ? $this.prop('checked', true) : $this.prop('checked', false);
                                        } else if ($this[0].type == 'radio') {
                                            data[key] == $this.attr('value') ? $this.prop('checked', true) : $this.prop('checked', false);
                                        } else {
                                            $this.val(data[key]);
                                        }
                                    }
                                    _.set_data($this, data[key]);
                                }

                            }

                    }

                })
            }

            // value forward
            var $value_forward = $elm.data('iwptp-value-forward');
            if ($value_forward) {
                $value_forward.val(data);
            }

            // content templates
            var $content_templates = $elm.data('iwptp-content-templates');
            if ($content_templates) {
                $content_templates.each(function () {
                    var $content_template = $(this),
                        key = $content_template.attr('iwptp-content-template'),
                        val = $elm.data('iwptp-data')[key],
                        modifier = $content_template.attr('iwptp-value-modifier');
                    if (modifier) {
                        val = _.value_modifiers[modifier](val);
                    }
                    val = String(val).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
                    $content_template.text(val);
                })
            }

            // panel conditions
            var $panel_conditions = $elm.data('iwptp-panel-conditions');
            if ($panel_conditions) {
                $panel_conditions.each(function () {
                    var $this = $(this),
                        condition_key = $this.attr('iwptp-panel-condition');

                    if (_.panel_conditions[condition_key]($this, data)) {
                        $this.removeAttr('iwptp-disabled');
                    } else {
                        $this.attr('iwptp-disabled', '');
                    }
                })
            }

            // call controller
            if ($elm.attr('iwptp-controller')) {
                var controller_name = $elm.attr('iwptp-controller');
                if (typeof _.controllers[controller_name] !== 'undefined') {
                    _.controllers[controller_name]($elm, data);
                }
            }

        },

        reindex_rows: function ($wrapper) {
            var $children = $wrapper.data('iwptp-children'),
                $removed = $(),
                wrapper_data = [];
            $children.each(function () {
                var $child = $(this),
                    child_index = $wrapper.children('[iwptp-model-key="[]"]').index($child);
                child_data = $child.data('iwptp-data');
                // does the node exist?
                if (child_index !== -1) {
                    wrapper_data[child_index] = child_data;
                    $child.attr('iwptp-model-key-index', child_index);
                } else {
                    $removed = $removed.add($child);
                }
            })
            $wrapper.data('iwptp-data', wrapper_data);
            $wrapper.data('iwptp-children', $children.not($removed));
            // html class
            $wrapper.data('iwptp-children').removeClass('iwptp-first-row iwptp-last-row')
            //-- first row
            $wrapper.data('iwptp-children').filter(function () {
                return $(this).attr('iwptp-model-key-index') == 0;
            }).addClass('iwptp-first-row');
            //-- last row
            $wrapper.data('iwptp-children').filter(function () {
                return $(this).attr('iwptp-model-key-index') == $wrapper.data('iwptp-children').length - 1;
            }).addClass('iwptp-last-row');

            $wrapper.change();
        },

        /* controllers */

        controllers: {

            /*  controller: column row */

            column_row: function ($elm, data, e) {
                // property
                if (data.type == 'property') {
                    var prop = data.property ? data.property : $elm.data('iwptp-children').filter('[iwptp-model-key="property"]').val();

                    // build attributes
                    var attrs = '';
                    // use prop like image-size as size in sc attrs
                    $.each(data, function (key, val) {
                        if (key.substring(0, prop.length) == prop) {
                            attrs += ' ' + key.substring(prop.length + 1) + '="' + val + '" ';
                        }
                    })

                    data.content = '[' + prop + attrs + ']';
                    $elm.data('iwptp-children').filter("[iwptp-model-key='template']").val(data.content);

                    // custom field
                } else if (data.type == 'custom_field') {
                    var key = data.custom_field ? data.custom_field : '';
                    data.content = '[custom_field key="' + key + '"]';
                    $elm.data('iwptp-children').filter("[iwptp-model-key='template']").val(data.content);

                    // attribute
                } else if (data.type == 'attribute') {
                    var name = data.attribute ? data.attribute : '';
                    data.content = '[attribute name="' + name + '"]';
                    $elm.data('iwptp-children').filter("[iwptp-model-key='template']").val(data.content);

                    //buttons
                } else if (data.type == 'buttons') {
                    data.content = '';
                    if (data.buttons instanceof Array) {
                        $.each(data.buttons, function (index, button) {
                            var button_mkp = '[button ';
                            $.each(button, function (button_key, button_val) {
                                button_mkp += button_key + '="' + button_val + '" ';
                            })
                            button_mkp += ']';
                            data.content += button_mkp;
                        })
                    }
                    $elm.data('iwptp-children').filter("[iwptp-model-key='template']").val(data.content);
                }

            },

            /* controller: navigation: header row */
            header_row: function ($elm, data, e) {
                $elm.removeClass('iwptp-editor-columns-enabled-left iwptp-editor-columns-enabled-left-right iwptp-editor-columns-enabled-left-center-right');
                $elm.addClass('iwptp-editor-columns-enabled-' + data.columns_enabled);
            },

            /* controller: filters */

            filters: function ($elm, data, e) {
                if (!data || !data.length) {
                    $elm.prev('.iwptp-filter-headings').hide();
                } else {
                    $elm.prev('.iwptp-filter-headings').show();
                }
            },

        },

        row_templates: {},

        refresh_ids: function (item) {
            var _ = this

            $.each(item, function (key, val) {
                if (typeof val == 'object') {
                    item[key] = _.refresh_ids(val);
                }

                if (key == 'id') {
                    item[key] = Math.round(Math.random() * 1000000000);
                }
            })
            return item;
        },

        value_modifiers: {

            /* columns */
            escape_html: function (val) {
                if (!val) return false;
                return val.replace(/[\u00A0-\u9999<>\&]/gim, function (i) { return '&#' + i.charCodeAt(0) + ';'; });
            },

            /* filters */
            unlsug_filter_options_source: function (val) {
                var options = {
                    "product_cat": "Categories",
                    "product_tag": "Product tags",
                    "product_name": "Product names",
                    "attribute": "Attribute",
                    "price": "Price",
                    "rating": "Rating",
                    "custom_field": "Custom field",
                    "in_stock": "In stock",
                    "on_sale": "On sale",
                }

                return options[val];
            },

            unlsug_filter_template: function (val) {
                var options = {
                    "dropdown": "Dropdown",
                    "checkbox": "Checkbox",
                    "radio": "Radio",
                    "search": "Search",
                    "min_max": "Min-max",
                }

                return options[val];
            }

        },

        initial_data: {

            /* columns */

            column_row: {
                heading: '',
                template: '',
                orderby: 'title',
            },

            /* navigation: header row */

            header_row: {
                columns_enabled: 'left-right',
                columns: {
                    left: { template: '' },
                    center: { template: '' },
                    right: { template: '' },
                },
            },

        },

        panel_conditions: {

            prop: function ($elm, data) {

                var prop = $elm.attr('iwptp-condition-prop'),
                    val = $elm.attr('iwptp-condition-val'),
                    _data = $.extend({}, data);

                if (val === "true") {
                    return !!_data[prop];
                }

                if (val === "false") {
                    return !_data[prop];
                }

                if (val.substr(0, 1) === "!") {
                    val = val.substr(1);
                    return val != _data[prop];
                }

                if (
                    typeof _data[prop] != 'undefined' &&
                    _data[prop].constructor === Array
                ) { // array          
                    return -1 !== $.inArray(val, _data[prop]);
                }

                vals_arr = val.split('||');

                return -1 !== $.inArray(_data[prop], vals_arr);
            },

        },

        panel_enable: {
            custom_content: function ($elm) {
                $elm.find('.CodeMirror').each(function () {
                    this.CodeMirror.refresh();
                })
            }
        },

        panel_disable: {

        },

    }
    $(window).trigger('dominator_ui_ready');
})
