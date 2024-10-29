"use strict";

jQuery(document).ready(function($) {

    let mainTabs = [
        'query',
        'columns',
        'navigation',
        'settings',
        'style',
    ]
    let currentTab = (window.location.hash && $.inArray(window.location.hash.split('#')[1], mainTabs) !== -1) ? window.location.hash.split('#')[1] : 'query';

    window.location.hash = currentTab;

    iwptpOpenTab($('.iwptp-tabs-list li a[data-content="' + currentTab + '"]'));

    if (currentTab == 'columns') {
        iwptpColumnsRowTopFix($('.iwptp-editor-columns-container[iwptp-model-key="laptop"]'));
    }

    // Tabs
    $(document).on("click", ".iwptp-tabs-list > li > a", function(event) {
        event.preventDefault();
        $('.iwptp-editor-left-sidebar-help').trigger('click');

        if ($.inArray($(this).attr('data-content'), ['columns', 'style']) === -1) {
            window.location.hash = $(this).attr('data-content');
            iwptpOpenTab($(this));
        }
    });

    $('body').on('click', function(e) {
        // hide columns sub tab
        if ($(e.target).hasClass('iwptp-columns-has-sub-tab') === false) {
            $('.iwptp-columns-sub-tabs').fadeOut(150);
        }

        // hide style sub tab
        if ($(e.target).hasClass('iwptp-style-has-sub-tab') === false) {
            $('.iwptp-style-sub-tabs').fadeOut(150);
        }

    });

    $(document).on('click', '.iwptp-columns-has-sub-tab', function() {
        $(this).closest('li').find('.iwptp-columns-sub-tabs').fadeIn(150);
    });

    $(document).on('click', '.iwptp-style-has-sub-tab', function() {
        $(this).closest('li').find('.iwptp-style-sub-tabs').fadeIn(150);
    });

    $(document).on('click', '.iwptp-columns-sub-tabs a', function() {
        window.location.hash = 'columns';
        iwptpOpenTab($(this).closest('ul').closest('li').find('a'));

        $('.iwptp-editor-columns-container').hide();
        let target = $('.iwptp-editor-columns-container[data-iwptp-device="' + $(this).attr('data-iwptp-device') + '"]');
        target.show();


        setTimeout(function() {
            iwptpColumnsRowTopFix(target);
        }, 10);
    });

    $(document).on('click', '.iwptp-style-sub-tabs a', function() {
        window.location.hash = 'style';
        iwptpOpenTab($(this).closest('ul').closest('li').find('a'));

        $('html, body').animate({
            scrollTop: $("#iwptp-style-" + $(this).attr('data-section-id')).offset().top
        }, 1000);
    })

    $(document).on('change', '#iwptp-is-dark-theme', function() {
        let themeName;
        let switchElement = $(this).closest('.iwptp-switch');

        if ($(this).prop('checked') === true) {
            switchElement.find('.dark').show();
            switchElement.find('.light').hide();
            themeName = 'iwptp-dark-theme';
            $('body').removeClass('iwptp-light-theme');
            $('body').addClass('iwptp-dark-theme');
        } else {
            switchElement.find('.light').show();
            switchElement.find('.dark').hide();
            themeName = 'iwptp-light-theme';
            $('body').removeClass('iwptp-dark-theme');
            $('body').addClass('iwptp-light-theme');
        }

        iwptpUpdateTheme(themeName);
    })

    $(document).on('click', '#iwptp-left-sidebar-toggle', function(e) {
        if ($('body').hasClass('iwptp-editor-left-sidebar-hide')) {
            $('body').removeClass('iwptp-editor-left-sidebar-hide');
            $(this).removeClass('iwptp-left-sidebar-toggle-closed');
        } else {
            $('body').addClass('iwptp-editor-left-sidebar-hide');
            $(this).addClass('iwptp-left-sidebar-toggle-closed');
        }
    });

    $(document).on('click', '.iwptp-editor-left-sidebar-help', function(e) {
        $('.iwptp-block-editor-lightbox-screen').change().remove();
        $('.iwptp-left-sidebar-help').show();
    });

    $(document).on('click', '#iwptp-editor-preview', function() {
        $('.iwptp-modal-live-preview-device[data-device="laptop"]').trigger('click');
    });

    // full screen
    $(document).on('click', '#iwptp-full-screen', function() {
        $('body').toggleClass('iwptp-show-admin-bar');

        if ($('body').hasClass('iwptp-show-admin-bar')) {
            $(this).attr('title', 'Full Screen');
        } else {
            $(this).attr('title', 'Show WordPress Menu');
        }

        $(window).trigger('resize');
    });

    // delete product table
    $(document).on('click', '#iwptp-move-to-trash', function(e) {
        let deleteUrl = $(this).attr('data-url');

        swal({
            title: "Are you sure?",
            type: "warning",
            showCancelButton: true,
            cancelButtonClass: "iwptp-button iwptp-button-lg iwptp-button-white",
            confirmButtonClass: "iwptp-button iwptp-button-lg iwptp-button-blue",
            confirmButtonText: "Yes, i'm sure",
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
        }, function(isConfirm) {
            if (isConfirm) {
                setTimeout(function() {
                    window.location = deleteUrl;
                }, 100)
            }
        });
    });

    $(document).on('click', '.iwptp-columns-delete-column', function() {
        let btn = $(this);
        btn.closest('.iwptp-editor-columns-container').find('.iwptp-column-settings[iwptp-model-key-index="' + btn.closest('a').attr('data-iwptp-index') + '"]').find('.iwptp-editor-row-remove').trigger('click');
    });

    $(document).on('click', '.iwptp-block-editor-add-element', function() {
        $('.iwptp-element-block').removeClass('iwptp-element-selected');
    });

    // copy shortcode
    $(document).on('click', '.iwptp-product-table-shortcode-copy', function(e) {
        $('body').find('.tipsy').remove();
        iwptpCopyToClipboard('iwptp-product-table-shortcode');
        let button = $(this);
        button.find("span.iwptp-copied").fadeIn();
        setTimeout(function() {
            button.find("span.iwptp-copied").fadeOut();
        }, 1000);
    });

    $(document).on('change', '#iwptp-editor-mini-cart-type', function() {
        let subFieldsElement = $('.iwptp-editor-mini-cart-type-sub-fields');
        subFieldsElement.find('.iwptp-for-default').hide();
        subFieldsElement.find('.iwptp-editor-row-option').hide();

        if ($(this).val() == 'default') {
            console.log(subFieldsElement.find('.iwptp-for-default'))
            subFieldsElement.find('.iwptp-for-default').show();
            subFieldsElement.find('.iwptp-for-default .iwptp-editor-row-option').show();
        }

        if ($(this).val() == 'inline_mode') {
            subFieldsElement.find('.iwptp-editor-row-option[data-name="subtotal"]').show();
            subFieldsElement.find('.iwptp-editor-row-option[data-name="empty_cart_button"]').show();
            subFieldsElement.find('.iwptp-editor-row-option[data-name="view_checkout_button"]').show();
            subFieldsElement.find('.iwptp-editor-row-option[data-name="view_cart_button"]').show();
        }

        if ($(this).val() == 'float_side') {
            subFieldsElement.find('.iwptp-editor-row-option').show();
        }

        if ($(this).val() == 'float_toggle') {
            subFieldsElement.find('.iwptp-editor-row-option').show();

            subFieldsElement.find('.iwptp-editor-row-option[data-name="title"]').hide();
            subFieldsElement.find('.iwptp-editor-row-option[data-name="position"]').hide();
            subFieldsElement.find('.iwptp-editor-row-option[data-name="button_text"]').hide();
        }
    });

    $(document).on('change', '#iwptp-editor-navigation-sidebar-position', function() {
        if ($(this).val() == 'left_toggle_sidebar' || $(this).val() == 'right_toggle_sidebar') {
            $('.iwptp-navigation-left-sidebar-settings-item[data-name="sidebar_button_tooltip"]').show();
            $('.iwptp-navigation-left-sidebar-settings-item[data-name="sidebar_button_icon"]').show();
        } else {
            $('.iwptp-navigation-left-sidebar-settings-item[data-name="sidebar_button_tooltip"]').hide();
            $('.iwptp-navigation-left-sidebar-settings-item[data-name="sidebar_button_icon"]').hide();
        }
    });

    $(document).on('click', '#iwptp-modal-live-preview .iwptp-left-sidebar-toggle', function() {
        $(this).closest('.iwptp-left-sidebar').toggleClass('iwptp-left-sidebar-opened');
    });

    select2Init();

    $(window).on('resize', function() {
        iwptpFixElementSettingsSize();
    });

    $(document).on('click', '.iwptp-media-image-button', function(e) {
        e.preventDefault();

        let target = ($(this).attr("data-target") !== undefined && $(this).attr("data-target") != '') ? $($(this).attr("data-target")) : '';
        let type = $(this).attr("data-type");
        let mediaUploader;

        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        if (type === "single") {
            mediaUploader = wp.media.frames.file_frame = wp.media({
                title: "Choose Image",
                button: {
                    text: "Choose Image"
                },
                multiple: false
            });
        } else {
            mediaUploader = wp.media.frames.file_frame = wp.media({
                title: "Choose Images",
                button: {
                    text: "Choose Images"
                },
                multiple: true
            });
        }

        mediaUploader.on("select", function() {
            let attachment = mediaUploader.state().get("selection").toJSON();
            if (target != '') {
                target.find('[data-type="image_id"]').val(attachment[0].id).trigger('change');
                target.find('[data-type="image_url"]').val(attachment[0].url).trigger('change');
                target.find('[data-type="preview"]').html('<img src="' + attachment[0].url + '" width="43" height="43" alt=""><button type="button" class="iwptp-media-image-delete-button">x</button>');
            }
        });

        mediaUploader.open();
    });

    $(document).on('click', '.iwptp-media-image-delete-button', function() {
        let element = $(this).closest('div[data-type="media_image_target"]');

        swal({
            title: "Are you sure?",
            type: "warning",
            showCancelButton: true,
            cancelButtonClass: "iwptp-button iwptp-button-lg iwptp-button-white",
            confirmButtonClass: "iwptp-button iwptp-button-lg iwptp-button-blue",
            confirmButtonText: "Yes, i'm sure",
            closeOnConfirm: true
        }, function(isConfirm) {
            if (isConfirm) {
                if (element.length > 0) {
                    element.find('input[data-type="image_id"]').val('').trigger('change');
                    element.find('input[data-type="image_url"]').val('').trigger('change');
                    element.find('div[data-type="preview"]').html('');
                }
            }
        });
    });

    $(document).on('click', '#iwptp-import-laptop-cols', function() {
        swal({
            title: "Are you sure?",
            type: "warning",
            showCancelButton: true,
            cancelButtonClass: "iwptp-button iwptp-button-lg iwptp-button-white",
            confirmButtonClass: "iwptp-button iwptp-button-lg iwptp-button-blue",
            confirmButtonText: "Yes, i'm sure",
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
        }, function(isConfirm) {
            if (isConfirm) {
                setTimeout(function() {
                    let container = $('.iwptp-editor-columns-container[iwptp-model-key="tablet"]');
                    if (container.find('.iwptp-column-settings').length < 1) {
                        container.find('.iwptp-columns-add-row-without-animation').trigger('click');
                        iwptp.data.columns['tablet'] = [];
                    }

                    $.each(iwptp.data.columns['laptop'], function(index, col) {
                        iwptp.data.columns['tablet'].push(dominator_ui.refresh_ids($.extend(true, {}, col)));
                    })

                    dominator_ui.init($('.iwptp-editor-tab-columns'), iwptp.data.columns);

                    setTimeout(function() {
                        iwptpColumnsRowTopFix(container);
                        swal("Imported!", "", "success");
                    }, 50);
                }, 100);
            }
        });
    });

    $(document).on('click', '#iwptp-import-tablet-cols', function() {
        swal({
            title: "Are you sure?",
            type: "warning",
            showCancelButton: true,
            cancelButtonClass: "iwptp-button iwptp-button-lg iwptp-button-white",
            confirmButtonClass: "iwptp-button iwptp-button-lg iwptp-button-blue",
            confirmButtonText: "Yes, i'm sure",
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
        }, function(isConfirm) {
            if (isConfirm) {
                setTimeout(function() {
                    let container = $('.iwptp-editor-columns-container[iwptp-model-key="phone"]');
                    if (container.find('.iwptp-column-settings').length < 1) {
                        container.find('.iwptp-columns-add-row-without-animation').trigger('click');
                        iwptp.data.columns['phone'] = [];
                    }

                    $.each(iwptp.data.columns['tablet'], function(index, col) {
                        iwptp.data.columns['phone'].push(dominator_ui.refresh_ids($.extend(true, {}, col)));
                    })

                    dominator_ui.init($('.iwptp-editor-tab-columns'), iwptp.data.columns);

                    setTimeout(function() {
                        iwptpColumnsRowTopFix(container);
                        swal("Imported!", "", "success");
                    }, 50);
                }, 100)
            }
        });
    });

    $(document).on('click', '.iwptp-style-save-as-new-preset-button', function() {
        let name = $('#iwptp-style-preset-name-input').val();
        let image_url = $('#iwptp-style-preset-image-url-input').val();

        if (name == '') {
            swal("Name is required", "", "warning");
            return;
        }

        if (image_url == '') {
            swal("Image is required", "", "warning");
            return;
        }

        swal({
            title: "Are you sure?",
            type: "warning",
            showCancelButton: true,
            cancelButtonClass: "iwptp-button iwptp-button-lg iwptp-button-white",
            confirmButtonClass: "iwptp-button iwptp-button-lg iwptp-button-blue",
            confirmButtonText: "Yes, i'm sure",
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
        }, function(isConfirm) {
            if (isConfirm) {
                setTimeout(function() {
                    jQuery.ajax({
                        url: IWPTPL_DATA.ajax_url,
                        type: 'post',
                        dataType: 'json',
                        data: {
                            action: 'iwptp_create_style_preset',
                            nonce: IWPTP_DATA.ajax_nonce,
                            preset_name: name,
                            preset_image_url: image_url,
                            preset_data: JSON.stringify(iwptp.data.style),
                        },
                        success: function(response) {
                            if (response.success) {
                                $('.iwptp-style-preset-items').html(response.presets).ready(function() {
                                    swal("Saved", "", "success");
                                });
                            } else {
                                swal("Error", "", "danger");
                            }
                        },
                        error: function() {}
                    })
                }, 100)
            }
        });
    });

    $(document).on('click', '.iwptp-style-preset-delete-button', function() {
        let presetSlug = $(this).val();
        let presetItem = $(this).closest('.iwptp-style-preset-item');

        swal({
            title: "Are you sure?",
            type: "warning",
            showCancelButton: true,
            cancelButtonClass: "iwptp-button iwptp-button-lg iwptp-button-white",
            confirmButtonClass: "iwptp-button iwptp-button-lg iwptp-button-blue",
            confirmButtonText: "Yes, i'm sure",
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
        }, function(isConfirm) {
            if (isConfirm) {
                setTimeout(function() {
                    jQuery.ajax({
                        url: IWPTPL_DATA.ajax_url,
                        type: 'post',
                        dataType: 'json',
                        data: {
                            action: 'iwptp_delete_style_preset',
                            nonce: IWPTP_DATA.ajax_nonce,
                            preset_slug: presetSlug,
                        },
                        success: function(response) {
                            if (response.success === true) {
                                presetItem.remove();
                                swal("Deleted", "", "success");
                            } else {
                                swal("Error", "", "danger");
                            }
                        },
                        error: function() {}
                    })
                }, 100)
            }
        });
    });

    $(document).on('click', '.iwptp-style-preset-import-button', function() {
        let data = $(this).closest('.iwptp-style-preset-item').find('.iwptp-style-preset-item-data').val();

        swal({
            title: "Are you sure?",
            type: "warning",
            showCancelButton: true,
            cancelButtonClass: "iwptp-button iwptp-button-lg iwptp-button-white",
            confirmButtonClass: "iwptp-button iwptp-button-lg iwptp-button-blue",
            confirmButtonText: "Yes, i'm sure",
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
        }, function(isConfirm) {
            if (isConfirm) {
                setTimeout(function() {
                    iwptp.data.style = $.parseJSON(data);
                    dominator_ui.init($('.iwptp-editor-tab-style[iwptp-model-key="style"]'), iwptp.data.style);

                    swal("Imported", "", "success");
                }, 100)
            }
        });
    });

    $('#iwptp-editor-navigation-sidebar-position').trigger('change');

    $(document).on('click', '.iwptp-tab-label', function() {
        $('.iwptp-element-settings').html('');
    })

    if ($('div[data-type="media_image_target"]').length > 0) {
        $.each($('div[data-type="media_image_target"]'), function() {
            if ($(this).find('input[data-type="image_id"]').val() != '' && $(this).find('input[data-type="image_url"]').val() != '') {
                $(this).find('div[data-type="preview"]').html('<img src="' + $(this).find('input[data-type="image_url"]').val() + '" width="43" height="43" alt=""><button type="button" class="iwptp-media-image-delete-button">x</button>');
            }
        })
    }

    $(document).on('click', '.iwptp-element-settings-tab-item', function() {
        let lightbox = $(this).closest('.iwptp-block-editor-lightbox-screen:visible');
        lightbox.find('.iwptp-element-settings-tabs li a').removeClass('active')
        $(this).addClass('active');
        lightbox.find('.iwptp-element-settings-content-item').removeClass('active');
        lightbox.find('.iwptp-element-settings-content-item[data-content="' + $(this).attr('data-content') + '"]').addClass('active');
    });

    $(document).on('select2:select', '.iwptp-query-product-status', function(e) {
        let data = e.params.data;
        let $this = $(this);
        let values = $this.val();

        if ($.inArray(data.id, ['only_in_stock']) !== -1 && $.inArray('only_out_of_stock', values) !== -1) {
            values = $.grep(values, function(value) {
                return value != 'only_out_of_stock';
            });
        }

        if ($.inArray(data.id, ['only_out_of_stock']) !== -1 && $.inArray('only_in_stock', values) !== -1) {
            values = $.grep(values, function(value) {
                return value != 'only_in_stock';
            });
        }

        setTimeout(function() {
            $this.val(values).change();
        }, 100);
    });

    $(document).on('select2:open', '#iwptp-editor-left-sidebar .iwptp-select2', function(e) {
        let className = ($('#iwptp-is-dark-theme').prop('checked') === true) ? 'iwptp-dark-theme' : 'iwptp-light-theme';
        $('.select2-container--open').removeClass('iwptp-dark-theme').removeClass('iwptp-light-theme').addClass(className);
    });

    $(document).on('click', '.iwptp-modal-live-preview-device', function() {
        $('.iwptp-modal-live-preview-device').removeClass('selected');
        $('#iwptp-modal-live-preview .iwptp-table-scroll-wrapper-outer').hide();
        jQuery('.iwptp-modal-loading').show();
        $(this).addClass('selected');

        if ($(this).attr('data-device') == 'phone') {
            $(this).closest('#iwptp-modal-live-preview').addClass('iwptp-preview-phone')
        } else {
            $(this).closest('#iwptp-modal-live-preview').removeClass('iwptp-preview-phone')
        }

        iwptpGetLivePreview($(this).closest('#iwptp-modal-live-preview').attr('data-table-id'), $(this).attr('data-device'));
    });

    $(document).on('change', '.iwptp-font-family-dropdown', function(e) {
        if ($(this).val() != '' && $.inArray($(this).val(), ['', 'Arial', 'Tahoma', 'Verdana', 'Helvetica', 'Times New Roman', 'Trebuchet MS', 'Georgia']) === -1 && $('html head').find('link#iwptp-google-font-' + $(this).val()).length < 1) {
            let fontUrl = 'https://fonts.googleapis.com/css?family=' + $(this).val() + ':100,100italic,200,200italic,300,300italic,400,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic';
            let link = '<link id="iwptp-google-font-' + $(this).val() + '" href="' + fontUrl + '" rel="stylesheet" type="text/css">';
            $('html head').find('link').last().after(link);
        }

        $(this).closest('div').find('.iwptp-font-family-sample-text').css({
            "font-family": '"' + $(this).val() + '"'
        })
    });

    if ($('.iwptp-font-family-dropdown').length > 0) {
        $('.iwptp-font-family-dropdown').trigger('change');
    }

    iwptpSetTipsyTooltip();
})

function iwptpOpenTab(item) {
    jQuery('.iwptp-editor-left-sidebar-help-item').hide();
    jQuery('.iwptp-left-sidebar-help').show();

    let iwptpTabItem = item;
    let iwptpParentContent = iwptpTabItem.closest(".iwptp-tabs-list");
    let iwptpParentContentID = iwptpParentContent.attr("data-content-id");
    let iwptpDataBox = iwptpTabItem.attr("data-content");
    iwptpParentContent.find("li a.selected").removeClass("selected");
    iwptpTabItem.addClass("selected");

    jQuery('.iwptp-editor-left-sidebar-help-item[data-section="' + iwptpDataBox + '"]').show();

    jQuery("#" + iwptpParentContentID).children("div.selected").removeClass("selected");
    jQuery("#" + iwptpParentContentID + " div[data-content=" + iwptpDataBox + "]").addClass("selected");
}

function iwptpUpdateTheme(themeName) {
    jQuery.ajax({
        method: 'post',
        dataType: 'json',
        url: IWPTPL_DATA.ajax_url,
        data: {
            action: 'iwptp_editor_update_theme',
            nonce: IWPTP_DATA.ajax_nonce,
            theme_name: themeName
        },
        success: function(response) {},
        error: function(e) {},
    })
}

function iwptpGetLivePreview(tableId, deviceName = 'laptop') {
    jQuery.ajax({
        method: 'post',
        dataType: 'json',
        url: IWPTPL_DATA.ajax_url,
        data: {
            action: 'iwptp_get_preview',
            nonce: IWPTP_DATA.ajax_nonce,
            table_id: tableId,
            device: deviceName,
            table_data: JSON.stringify(iwptp.data)
        },
        success: function(response) {
            if (response.success === true) {
                jQuery('#iwptp-modal-live-preview-container').html(response.preview).ready(function(e) {
                    jQuery('#iwptp-modal-live-preview .iwptp-table-scroll-wrapper-outer').hide();
                    jQuery('#iwptp-modal-live-preview-container .iwptp-device-' + deviceName).show();

                    jQuery('#iwptp-modal-live-preview .iwptp-modal-box').css({
                        height: '80%'
                    });

                    jQuery('#iwptp-modal-live-preview .iwptp-admin-modal-content').css({
                        height: '92%'
                    });

                    jQuery('#iwptp-modal-live-preview .iwptp-modal-body').css({
                        height: '90%'
                    });

                    jQuery('#iwptp-modal-live-preview-container').find('a').attr('href', 'javascript:;');
                });
            } else {
                swal("Error!", "", "warning");
            }

            jQuery('.iwptp-modal-loading').hide();
        },
        error: function(e) {
            jQuery('.iwptp-modal-loading').hide();
            swal("Error!", "", "warning");
        },
    })
}

function iwptpFixElementSettingsSize() {
    let height = parseInt(jQuery('.iwptp-editor-left-sidebar-wrapper').height()) - parseInt(jQuery('.iwptp-editor-left-sidebar-plugin-name').height()) - parseInt(jQuery('.iwptp-left-sidebar-bottom').height()) - 30;
    jQuery('.iwptp-block-editor-lightbox-content-rows:visible').css({
        height: height + 'px'
    });
}

function iwptpColumnsRowTopFix(container) {
    if (container.length < 1) {
        return;
    }

    let marginTop = (parseInt(container.find('.iwptp-editor-light-heading-fixed').height()) + 26);
    container.css({ "margin-top": marginTop });
}

function iwptpSetTipsyTooltip() {
    jQuery('[title]').tipsy({
        html: true,
        arrowWidth: 10,
        attr: 'data-tipsy',
        cls: null,
        duration: 150,
        offset: 7,
        position: 'top-center',
        trigger: 'hover',
        onShow: null,
        onHide: null
    });
}


function iwptpLoadingStart() {
    jQuery('#iwptp-loading').removeClass('iwptp-loading-error').removeClass('iwptp-loading-success').text('Loading ...').slideDown(300);
}

function iwptpLoadingSuccess(message = 'Success') {
    jQuery('#iwptp-loading').removeClass('iwptp-loading-error').addClass('iwptp-loading-success').text(message).delay(1500).slideUp(200);
}

function iwptpLoadingError(message = 'Error !') {
    jQuery('#iwptp-loading').removeClass('iwptp-loading-success').addClass('iwptp-loading-error').text(message).delay(1500).slideUp(200);
}

function select2Init() {
    if (jQuery.fn.select2) {
        if (jQuery('.iwptp-select2').length > 0) {
            jQuery('.iwptp-select2').select2();
        }

        if (jQuery('.iwptp-select2-user-roles').length > 0) {
            let query;
            jQuery(".iwptp-select2-user-roles").select2({
                ajax: {
                    type: "post",
                    delay: 800,
                    url: IWPTPL_DATA.ajax_url,
                    dataType: "json",
                    data: function(params) {
                        query = {
                            action: "iwptp_get_user_roles",
                            nonce: IWPTP_DATA.ajax_nonce,
                            search: params.term
                        };
                        return query;
                    }
                },
                placeholder: 'Select ...',
                minimumInputLength: 2
            });
        }

        if (jQuery('.iwptp-select2-products-variations').length > 0) {
            let query;
            jQuery(".iwptp-select2-products-variations").select2({
                ajax: {
                    type: "post",
                    delay: 800,
                    url: IWPTPL_DATA.ajax_url,
                    dataType: "json",
                    data: function(params) {
                        query = {
                            action: "iwptp_get_products_variations",
                            nonce: IWPTP_DATA.ajax_nonce,
                            search: params.term
                        };
                        return query;
                    }
                },
                placeholder: 'Select ...',
                minimumInputLength: 2
            });
        }

        if (jQuery('.iwptp-select2-taxonomies').length > 0) {
            let query;
            jQuery(".iwptp-select2-taxonomies").select2({
                ajax: {
                    type: "post",
                    delay: 800,
                    url: IWPTPL_DATA.ajax_url,
                    dataType: "json",
                    data: function(params) {
                        query = {
                            action: "iwptp_get_taxonomies",
                            nonce: IWPTP_DATA.ajax_nonce,
                            search: params.term
                        };
                        return query;
                    }
                },
                placeholder: 'Select ...',
                minimumInputLength: 2
            });
        }

        if (jQuery('.iwptp-select2-users').length > 0) {
            let query;
            jQuery(".iwptp-select2-users").select2({
                ajax: {
                    type: "post",
                    delay: 800,
                    url: IWPTPL_DATA.ajax_url,
                    dataType: "json",
                    data: function(params) {
                        query = {
                            action: "iwptp_get_users",
                            nonce: IWPTP_DATA.ajax_nonce,
                            search: params.term
                        };
                        return query;
                    }
                },
                placeholder: 'Select ...',
                minimumInputLength: 2
            });
        }

        jQuery('.iwptp-select2-icons').select2({
            templateResult: function(icon) {
                if (icon != undefined && icon.text != '') {
                    var img = (icon.id != '') ? '<img class="iwptp-icon-rep" src="' + iwptp_icons_url + '/' + icon.id + '.svg">' : '',
                        jQueryicon = jQuery('<span>' + img + '<span class="iwptp-icon-name">' + icon.text + '</span>' + '</span>');
                    return jQueryicon;
                }
            },
            templateSelection: function(icon) {
                if (icon != undefined && icon.text != '') {
                    var img = (icon.id != '') ? '<img class="iwptp-icon-rep" src="' + iwptp_icons_url + '/' + icon.id + '.svg">' : '',
                        jQueryicon = jQuery('<span>' + img + '<span class="iwptp-icon-name">' + icon.text + '</span>' + '</span>');
                    return jQueryicon;
                }
            },
        });

        setTimeout(function() {
            if (jQuery('.select2-container').length > 0) {
                jQuery('.select2-container').css({ width: "100%" })
            }

            jQuery(window).trigger('scroll');
        }, 150);
    }
}