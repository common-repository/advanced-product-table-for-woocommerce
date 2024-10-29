"use strict";

jQuery(document).ready(function ($) {
    // copy shortcode in list
    $(document).on('click', '.iwptp-product-table-list-shortcode-copy-button', function (e) {
        iwptpCopyToClipboard($(this).attr('data-target'));
        let button = $(this);
        button.find("span.iwptp-copied").fadeIn();
        setTimeout(function () {
            button.find("span.iwptp-copied").fadeOut();
        }, 1000);
    });
});

function iwptpCopyToClipboard(containerId) {
    let $temp = jQuery("<textarea>");
    let brRegex = /<br\s*[\/]?>/gi;
    jQuery("body").append($temp);
    $temp.val(jQuery("#" + containerId).html().replace(brRegex, "\r\n").replace(/<\/?[a-zA-Z]+\/?>/g, '').trim()).select();
    document.execCommand("copy");
    $temp.remove();
}