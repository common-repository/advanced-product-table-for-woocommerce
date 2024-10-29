"use strict";

jQuery(document).ready(function ($) {
    let mainTabs = [
        'general',
        'localization'
    ]

    let currentTab = (window.location.hash && $.inArray(window.location.hash.split('#')[1], mainTabs) !== -1) ? window.location.hash.split('#')[1] : 'general';
    window.location.hash = currentTab;

    iwptpOpenTab($('.iwptp-tabs-list li a[data-content="' + currentTab + '"]'));

    // Tabs
    $(document).on("click", ".iwptp-tabs-list > li > a", function (event) {
        event.preventDefault();
        window.location.hash = $(this).attr('data-content');
        iwptpOpenTab($(this));
    });
});

function iwptpOpenTab(item) {
    let iwptpTabItem = item;
    let iwptpParentContent = iwptpTabItem.closest(".iwptp-tabs-list");
    let iwptpParentContentID = iwptpParentContent.attr("data-content-id");
    let iwptpDataBox = iwptpTabItem.attr("data-content");
    iwptpParentContent.find("li a.selected").removeClass("selected");
    iwptpTabItem.addClass("selected");

    jQuery("#" + iwptpParentContentID).find(".iwptp-tab-content-item.selected").removeClass("selected");
    jQuery("#" + iwptpParentContentID + " div[data-content=" + iwptpDataBox + "]").addClass("selected");
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