jQuery(function ($) {
    // reload page with preset slug param
    $('.iwptp-predefined-preset-item:not(.iwptp-disabled)').on('click', function (e) {
        if ($(e.target).hasClass('iwptp-presets__item__image__cover') === false && $.inArray($(e.target).prop('tagName'), ['svg', 'path', 'SVG', 'PATH']) === -1) {
            $('.iwptp-predefined-preset-item').removeClass('selected')
            $(this).addClass('selected');
            $('#iwptp-predefined-template').val($(this).attr('data-preset-slug'));
        }
    });

    $('.iwptp-colored-preset-item:not(.iwptp-disabled)').on('click', function (e) {
        if ($(e.target).hasClass('iwptp-presets__item__image__cover') === false && $.inArray($(e.target).prop('tagName'), ['svg', 'path', 'SVG', 'PATH']) === -1) {
            $('.iwptp-colored-preset-item').removeClass('selected')
            $(this).addClass('selected');
            $('#iwptp-colored-template').val($(this).attr('data-preset-slug'));
        }
    });

    $('.iwptp-new-template-get-start-button').on('click', function () {
        let predefined = $('#iwptp-predefined-template').val();
        let colored = $('#iwptp-colored-template').val();

        let hash = location.hash;
        let url = location.href.replace(location.hash, "")

        window.location.href = url + '&iwptp_predefined_preset=' + predefined + '&iwptp_colored_preset=' + colored + hash;
    });

    // dismiss preset applied message
    $('.iwptp-preset-applied-message__dismiss').on('click', function () {
        var $this = $(this);
        $this.closest('.iwptp-preset-applied-message').slideUp();
    })

    // copy shortcode
    $('.iwptp-preset-applied-message__shortcode-copy-button').on('click', function () {
        var $this = $(this);
        $input = $this.siblings('input');
        $input.select();
        document.execCommand("copy");
    });

    $(document).on('click', '.iwptp-presets__item__image__cover', function () {
        let imgSrc = $(this).closest('.iwptp-presets__item__image').find('img').attr('src');

        $('#iwptp-modal-preset-image-preview').find('.iwptp-preset-image-preview').html('<img src="' + imgSrc + '">');
    });
})