jQuery(document).ready(function ($) {
    $(document).on('click', '#iwptp-activation-activate', function () {
        $('#iwptp-activation-type').val('activate');

        if ($('#iwptp-activation-email').val() != '') {
            if ($('#iwptp-activation-industry').val() != '') {
                setTimeout(function () {
                    $('#iwptp-activation-form').first().submit();
                }, 200)
            } else {
                swal({
                    title: "Industry is required !",
                    type: "warning"
                });
            }
        } else {
            swal({
                title: "Email is required !",
                type: "warning"
            });
        }
    });

    $(document).on('click', '#iwptp-activation-skip', function () {
        $('#iwptp-activation-type').val('skip');

        setTimeout(function () {
            $('#iwptp-activation-form').first().submit();
        }, 200)
    });
})