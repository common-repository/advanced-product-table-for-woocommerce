(function ($, ctrl) {

    ctrl.get_parent = function (elm) {
        return $(elm).closest('.iwptp-block-editor').data('iwptp_block_editor');
    };

    ctrl.add_element = function (element, row_index, elm_index) {
        var model = IWPTPL_Block_Editor.Ctrl.get_parent(this).model;
        model.add_element(element, row_index, elm_index);
        model.parent.$elm.children('.iwptp-block-editor-row').eq(row_index).children('.iwptp-element-block').eq(elm_index).addClass('editing');
    }

    ctrl.remove_element = function (element, row_index, elm_index) {
        var model = IWPTPL_Block_Editor.Ctrl.get_parent(this).model;
        model.remove_element(element, row_index, elm_index);
    }

    ctrl.add_row = function (e) {
        e.preventDefault();

        var model = IWPTPL_Block_Editor.Ctrl.get_parent(this).model;
        model.add_row();
    }

    ctrl.edit_row = function (e) {
        var $this = $(this),
            $row = $(e.target).closest('.iwptp-block-editor-row'),
            row = $row.data('iwptp-data'),
            row_index = $row.index(),
            parent = IWPTPL_Block_Editor.Ctrl.get_parent(this);

        // row is not editable
        if (!parent.config.edit_row_partial) {
            return;
        }

        var $lightbox = parent.view.lightbox({
            partial: parent.config.edit_row_partial,
            attr: {
                'data-row-index': row_index,
            },
            $row: $row,
            duplicate_remove: true,
        });

        // transfer data to block from lightbox
        dominator_ui.init($lightbox, row);
        $lightbox.on('change', function () {
            parent.model.update_row($lightbox.data('iwptp-data'), row_index);
        });
    },

        ctrl.delete_row = function (e) {
            var $this = $(this),
                $row = $(e.target).closest('.iwptp-block-editor-row'),
                $sibling_rows = $row.siblings('.iwptp-block-editor-row'),
                row = $row.data('iwptp-data'),
                row_index = $row.index(),
                parent = IWPTPL_Block_Editor.Ctrl.get_parent(this);

            if (!parent.config.delete_row) {
                return;
            }

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
                    if (!$sibling_rows.length) {
                        parent.model.reset_row(row_index);
                    } else {
                        parent.model.remove_row(row_index);
                    }
                }
            });
        },

        ctrl.sort_update = function (e) { // for row or elements
            var $editor = $(e.target).closest('.iwptp-block-editor'),
                new_data = [],
                model = IWPTPL_Block_Editor.Ctrl.get_parent($editor).model;

            // iterate rows
            $editor.children('.iwptp-block-editor-row').each(function () {
                var $row = $(this),
                    id = $row.attr('data-id'),
                    new_row = model.get_row(id);

                // iterate elements
                /*
                  needs to be done in case the sort update
                  was triggered by change in element order
                */
                new_row.elements = [];
                $row.children('.iwptp-element-block').each(function () {
                    var $_element = $(this),
                        element = $_element.data('iwptp-data');

                    new_row.elements.push($.extend({}, element));
                });

                new_data.push(new_row);
            });

            model.set_data(new_data);
        }

    ctrl.edit_element = function (e) {
        $('.iwptp-element-block').removeClass('iwptp-element-selected');

        var $this = $(this),
            $row = $(e.target).closest('.iwptp-block-editor-row'),
            row_index = $row.index(),
            $element = $(e.target).closest('.iwptp-element-block'),
            element = $element.data('iwptp-data'),
            elm_index = $element.length ? $element.index() : $row.children('.iwptp-element-block').length,
            parent = IWPTPL_Block_Editor.Ctrl.get_parent(this);

        if ($(this).closest('.iwptp-element-settings').length < 1) {
            $('.iwptp-block-editor-lightbox-screen').remove();
            $('.iwptp-left-sidebar-help').show();
        }

        // delete
        if ($(e.target).closest('button.iwptp-element-block-delete').length > 0 || $(e.target).hasClass('button.iwptp-element-block-delete')) {
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
                    parent.model.remove_element(row_index, elm_index);
                    return false;
                }
            });

            return false;
        }

        // duplicate 
        if ($(e.target).closest('button.iwptp-element-block-duplicate').length > 0 || $(e.target).hasClass('button.iwptp-element-block-duplicate')) {
            parent.model.duplicate_element(row_index, elm_index);
            return false;
        }

        $element.addClass('iwptp-element-selected');
        if (!$('[data-iwptp-partial="' + element.type + '"]').length) {
            element.type = 'premium_version'
        }

        var $lightbox = parent.view.lightbox({
            partial: element.type,
            attr: {
                'data-row-index': row_index,
                'data-elm-index': elm_index,
                'data-partial': element.type,
                'iwptp-initial-data': 'element_' + element.type,
            },
            $element: $element,
            duplicate_remove: true,
        });

        if (element.type == 'premium_version') {
            $('.iwptp-block-editor-lightbox-screen:visible').find('.iwptp-element-settings-title').remove();
            $('.iwptp-block-editor-lightbox-screen:visible').find('.iwptp-element-settings-tabs').remove();
        } else {
            // transfer data to block from lightbox
            dominator_ui.init($lightbox, $.extend(true, {}, element));
            $lightbox.on('change', function () {
                parent.model.update_element($.extend(true, {}, $lightbox.data('iwptp-data')), row_index, elm_index);
                parent.view.mark_elm(row_index, elm_index);
            });

            if (jQuery('.iwptp-block-editor-lightbox-content .iwptp-element-settings-content-item[data-content="style"]>div').not('[iwptp-disabled]').length) {
                jQuery('.iwptp-element-settings-tabs .iwptp-element-settings-tab-item[data-content="style"]').show();
            } else {
                jQuery('.iwptp-element-settings-tabs .iwptp-element-settings-tab-item[data-content="style"]').hide();
            }

            // auto focus on Text, HTML element input
            $lightbox.find('[iwptp-model-key="text"], [iwptp-model-key="html"]').focus();

            $lightbox.find('[data-need-to-change="true"]').trigger('change');
        }
    },

        ctrl.add_element = function (e) {
            e.preventDefault();

            var $this = $(e.target),
                $row = $(e.target).closest('.iwptp-block-editor-row'),
                row_index = $row.index(),
                $element = $(e.target).closest('.iwptp-element-block'),
                elm_index = $element.length ? $element.index() : $row.children('.iwptp-element-block').length,
                parent = IWPTPL_Block_Editor.Ctrl.get_parent($(e.target)),
                $lightbox = parent.view.lightbox({
                    partial: parent.config.add_element_partial,
                    attr: {
                        'data-row-index': row_index,
                        'data-elm-index': elm_index,
                    },
                    duplicate_remove: false,
                });

            if ($(e.target).closest('#iwptp-editor-left-sidebar').length < 1 && $('.iwptp-block-editor-lightbox-screen').length > 1) {
                $('.iwptp-block-editor-lightbox-screen').first().change().remove();
            }

            // add element
            $lightbox.on('click', '.iwptp-block-editor-element-type:not(.iwptp-disabled)', function (e) {
                var element = {
                    id: Date.now(),
                    type: $(e.target).attr('data-elm'),
                    style: {},
                };
                parent.model.add_element(element, row_index, elm_index);
                // close this lightbox
                $lightbox.trigger('destroy');

                // trigger new element edit
                parent.$elm.children('.iwptp-block-editor-row').eq(row_index).children('.iwptp-element-block').eq(elm_index).click();
            })

        }

})(jQuery, IWPTPL_Block_Editor.Ctrl);