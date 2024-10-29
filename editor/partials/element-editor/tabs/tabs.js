var IWPTPL_Tabs = {};

(function($) {

    IWPTPL_Tabs = {

        $elm: $(),

        init: function(elm) {

            // instantiate sub objects
            this.view = Object.create(IWPTPL_Tabs.View);
            this.ctrl = Object.create(IWPTPL_Tabs.Ctrl);

            // parent reference
            this.view.parent = this;
            this.ctrl.parent = this;

            // relate $elm and this
            this.$elm = $(elm);
            this.$elm.data('iwptp_tabs', this);

            // inital view
            this.view.render();

            // attach controller
            $('> .iwptp-tab-triggers', this.$elm).on('click', '.iwptp-tab-trigger', this.ctrl.trigger_tab);
            $('> .iwptp-tab-triggers', this.$elm).on('click', '.iwptp-tab-disable', this.ctrl.disable_tab);
            $('> .iwptp-tab-triggers', this.$elm).on('click', '.iwptp-tab-enable', this.ctrl.enable_tab);
        },

        Ctrl: {
            get_parent: function(elm) {
                return $(elm).closest('.iwptp-tabs').data('iwptp_tabs');
            },

            trigger_tab: function(e) {
                var $trigger = $(e.target).closest('.iwptp-tab-trigger'),
                    tabs_instance = IWPTPL_Tabs.Ctrl.get_parent($trigger),
                    tab_index = $trigger.index();
                tabs_instance.view.open(tab_index);
            },

            disable_tab: function(e) {
                var $trigger = $(e.target).closest('.iwptp-tab-trigger'),
                    tabs_instance = IWPTPL_Tabs.Ctrl.get_parent($trigger),
                    tab_index = $trigger.attr('iwptp-index'),
                    $enable_trigger = $trigger.siblings('.iwptp-disabled-tabs').find('[iwptp-index="' + tab_index + '"]'),
                    $tabs = $trigger.closest('.iwptp-tabs');

                if ($trigger.hasClass('iwptp-selected-tab')) {
                    $trigger.hide();
                    $enable_trigger.show();

                    tabs_instance.view.open(0);
                    $tabs.trigger('tab_disabled', tab_index);
                    e.stopPropagation();
                }
            },

            enable_tab: function(e) {
                var $en_trigger = $(e.target).closest('.iwptp-tab-enable'),
                    tab_index = $en_trigger.attr('iwptp-index'),
                    $trigger = $en_trigger.closest('.iwptp-disabled-tabs').siblings('[iwptp-index="' + tab_index + '"]'),
                    tabs_instance = IWPTPL_Tabs.Ctrl.get_parent($trigger),
                    $tabs = $trigger.closest('.iwptp-tabs');
                $trigger.show();
                $en_trigger.hide();

                tabs_instance.view.open(tab_index);
                $tabs.trigger('tab_enabled', tab_index);
            },

            enable_tab_index: function(tab_index) {
                var $tabs = this.parent.$elm,
                    $tab = $('> .iwptp-tab-triggers > [iwptp-index="' + tab_index + '"]', $tabs),
                    $enable = $('> .iwptp-tab-triggers > .iwptp-disabled-tabs > [iwptp-index="' + tab_index + '"]', $tabs);
                $tab.show();
                $enable.hide();
            }
        },

        View: {

            parent,

            render: function() {
                var $tabs = this.parent.$elm,
                    $triggers_wrapper = $tabs.children('.iwptp-tab-triggers'),
                    $enable_wrapper = $triggers_wrapper.children('.iwptp-disabled-tabs');
                if (!$enable_wrapper.length) {
                    $enable_wrapper = $('<div class="iwptp-disabled-tabs">');
                    $triggers_wrapper.append($enable_wrapper);
                }

                $tabs.find('.iwptp-tab-trigger').each(function() {
                    var $trigger = $(this),
                        index = $trigger.index();
                    $trigger.attr('iwptp-index', index);
                    $trigger.siblings('')
                    if ($trigger.is('[iwptp-can-disable]')) {
                        $enable_wrapper.append('<span class="iwptp-tab-enable" iwptp-index="' + index + '">+ ' + $trigger.text() + '</span>');
                        if (!$trigger.children('.iwptp-tab-disable').length) {
                            var x = $('#iwptp-icon-x').length ? $('#iwptp-icon-x').html() : 'x';
                            $trigger.append(' <i class="iwptp-tab-disable">' + x + '</i>');
                        }
                        $trigger.hide();
                    }
                })

                this.open(0);
            },

            open: function(index) {
                var $tabs = this.parent.$elm;
                $tabs.children('.iwptp-tab-content').eq(index).show().siblings('.iwptp-tab-content').hide();

                $tabs.children('.iwptp-tab-triggers').children('.iwptp-tab-trigger')
                    .eq(index).addClass('iwptp-selected-tab')
                    .siblings().removeClass('iwptp-selected-tab');

                $tabs.children('.iwptp-tab-content').eq(index).addClass('iwptp-selected-tab');

            }
        }
    };

    $.fn.iwptp_tabs = function() {
        return this.each(function() {
            var tabs = $(this).data('iwptp_tabs');
            if (!tabs) {
                Object.create(IWPTPL_Tabs).init(this);
            }
        });
    };

})(jQuery)