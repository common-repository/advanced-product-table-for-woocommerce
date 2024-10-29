"use strict";

jQuery(document).ready(function($) {
    $('#wpbody-content').prepend('<div id="iwptp-main"> ' +
        '<div id="iwptp-header">' +
        '<div class="iwptp-plugin-title">' +
        '<span class="iwptp-plugin-name"><img src="' + IWPTPL.icon + '" alt="">' + IWPTPL.title + '</span>' +
        '<span class="iwptp-plugin-description">' + IWPTPL.description + '</span>' +
        '</div>' +
        '<div class="iwptp-header-left">' +
        '</div>' +
        '</div>' +
        '</div>');
})