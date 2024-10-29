"use strict";

jQuery(document).ready(function ($) {
    $(document).on("click", '[data-toggle="modal"]', function () {
        let modal = $($(this).attr("data-target"));

        modal.fadeIn();
        modal.find(".iwptp-modal-box").fadeIn();
        $("#iwptp-last-modal-opened").val($(this).attr("data-target"));

        // set height for modal body
        setTimeout(function () {
            iwptpFixModalHeight(modal);
        }, 150)
    });

    $(document).on("click", '[data-toggle="modal-close"]', function () {
        iwptpCloseModal();
    });

    $(document).on("keyup", function (e) {
        if (e.keyCode === 27) {
            iwptpCloseModal();
        }
    });
});

function iwptpFixModalHeight(modal) {
    let footerHeight = 0;
    let contentHeight = modal.find(".iwptp-admin-modal-content").height();
    let titleHeight = modal.find(".iwptp-modal-title").height();
    if (modal.find(".iwptp-modal-footer").length > 0) {
        footerHeight = modal.find(".iwptp-modal-footer").height() + 30;
    }
    let height = parseInt(contentHeight) - parseInt(titleHeight);
    if (modal.find('.iwptp-modal-top-search').length > 0) {
        height -= parseInt(modal.find('.iwptp-modal-top-search').height() + 40);
    }

    modal.find(".iwptp-admin-modal-content").css({
        "height": contentHeight + footerHeight
    });
    modal.find(".iwptp-modal-body").css({
        "height": height
    });
    modal.find(".iwptp-modal-box").css({
        "height": contentHeight + footerHeight
    });
}

function iwptpCloseModal() {
    // fix conflict with "Woo Invoice Pro" plugin

    jQuery('body').removeClass('_winvoice-modal-open');
    jQuery('._winvoice-modal-backdrop').remove();

    let lastModalOpened = jQuery('#iwptp-last-modal-opened');
    let modal = jQuery(lastModalOpened.val());
    if (lastModalOpened.val() !== '') {
        if (lastModalOpened.val() == '#iwptp-modal-live-preview') {
            // clear modal container
            modal.find('#iwptp-modal-live-preview-container').html('');
        }

        modal.find('.iwptp-modal-box').fadeOut();
        modal.fadeOut();
        lastModalOpened.val('');
    } else {
        jQuery('.iwptp-modal-box').fadeOut();
        jQuery('.iwptp-modal').fadeOut();
    }

    setTimeout(function () {
        modal.find('.iwptp-modal-box').css({
            height: 'auto',
            "max-height": '80%'
        });
        modal.find('.iwptp-modal-body').css({
            height: 'auto',
            "max-height": '90%'
        });
        modal.find('.iwptp-admin-modal-content').css({
            height: 'auto',
            "max-height": '92%'
        });
    }, 400);
}
