jQuery(function ($) {
    window.iwptp_feedback_anim = function (anim, $elm) {
        if (!$elm) {
            throw "Feedback anim: No $elm";
        }

        if (anim == 'move_row_up' || anim == 'move_row_down') {

            var $elm2 = (anim == 'move_row_up') ? $elm.next('[iwptp-model-key="[]"]') : $elm.prev('[iwptp-model-key="[]"]'),
                gap = parseInt($elm.css('margin-bottom')) ? parseInt($elm.css('margin-bottom')) : parseInt($elm2.css('margin-bottom')),
                offset = ($elm2.outerHeight() + gap) + 'px',
                offset2 = '-' + ($elm.outerHeight() + gap) + 'px';

            // re-flip positions

            // upper elm goes down by height of lower elm + margin
            $elm.css({
                'position': 'relative',
                'top': (anim == 'move_row_up') ? offset : offset2,
                'z-index': '1',
            });

            // lower elm goes down by height of upper elm + margin
            $elm2.css({
                'position': 'relative',
                'top': (anim == 'move_row_up') ? offset2 : offset,
                'z-index': '0',
            })

            $elm.add($elm2).animate({
                'top': 0,
            }, 500)

            if (window.scrollY > $elm.offset().top - gap) {
                $(window).scrollTop($(window).scrollTop() - ($elm.outerHeight() + gap));
            }


        } else if (anim == 'add_new_row') {
            var $placeholder = $('<div class="iwptp-row-plc-hld iwptp-anim-new-row"></div>');
            $placeholder
                .insertBefore($elm)
                .css({
                    'height': '0px',
                    'margin-bottom': $elm.css('margin-bottom'),
                    'opacity': '0',
                })
                .animate({
                    'height': $elm.outerHeight() + 'px',
                    'opacity': 1,
                }, 500, function () {
                    $placeholder
                        .css({
                            'margin-bottom': '-' + $elm.outerHeight() + 'px',
                        })
                        .fadeOut(500, function () {
                            $placeholder.remove();
                        });
                    $elm.fadeIn();
                });

            $elm.hide();

        } else if (anim == 'delete_row') {
            var $placeholder = $('<div class="iwptp-row-plc-hld iwptp-anim-delete-row"></div>');
            $placeholder
                .insertBefore($elm)
                .css({
                    'height': $elm.outerHeight() + 'px',
                    'margin-bottom': $elm.css('margin-bottom'),
                    'opacity': '1',
                })
                .animate({
                    'height': 0,
                    'opacity': 0,
                }, 500, function () {
                    $placeholder.remove();
                });

        } else if (anim == 'duplicate_row') {
            var duration = 1000;
            if ($elm.outerHeight() < 200) {
                duration = 250;
            }

            $elm.css({
                'opacity': .75,
                'position': 'relative',
                'top': '-' + ($elm.outerHeight() + parseInt($elm.css('margin-bottom'))) + 'px',
                'z-index': '1',
            });
            $elm.animate({
                'opacity': 1,
                'top': 0,
            }, duration, function () {
                $elm.css('z-index', 0);
            });
        }

    }
})
