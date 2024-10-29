<?php
if (!defined('ABSPATH')) {
    exit;
}

$iwptp_settings = iwptp_get_settings_data();
$settings = $iwptp_settings['modals'];

$labels = &$settings['labels'];
$locale = get_locale();

$strings = [];

if (!empty($labels)) {
    foreach ($labels as $key => $translations) {
        $strings[$key] = [];
        $translations = preg_split('/$\R?^/m', $translations);
        foreach ($translations as $translation) {
            $array = explode(':', $translation);
            if (!empty($array[1])) {
                $strings[$key][trim($array[0])] = stripslashes(trim($array[1]));
            } else {
                $strings[$key]['default'] = stripslashes(trim($array[0]));
            }
        }
    }
}

// maybe use defaults
foreach ($strings as $item => &$translations) {
    if (empty($translations[$locale])) {
        if (!empty($translations['default'])) {
            $translations[$locale] = $translations['default'];
        } else if (!empty($translations['en_US'])) {
            $translations[$locale] = $translations['en_US'];
        }
    }
}

$style = &$settings['style'];
?>
<div class="iwptp-nav-modal-tpl">
    <div class="iwptp-nav-modal" data-iwptp-table-id="<?php echo esc_attr($GLOBALS['iwptp_table_data']['id']); ?>">
        <div class="iwptp-nm-content iwptp-noselect">
            <div class="iwptp-nm-heading iwptp-nm-heading--sticky">
                <span class="iwptp-nm-close">
                    <?php echo wp_kses(iwptp_icon('x'), iwptp_allowed_html_tags()); ?>
                    </svg>
                </span>
                <span class="iwptp-nm-heading-text iwptp-on-filters-show">
                    <?php echo !empty($strings['filters'][$locale]) ? esc_html($strings['filters'][$locale]) : esc_html__('Filters', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                </span>
                <span class="iwptp-nm-heading-text iwptp-on-sort-show">
                    <?php echo !empty($strings['sort'][$locale]) ? esc_html($strings['sort'][$locale]) : esc_html__('Sort', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                </span>
                <div class="iwptp-nm-action">
                    <span class="iwptp-nm-reset">
                        <?php echo (!empty($strings['reset']) && !empty($strings['reset'][$locale])) ?  esc_html($strings['reset'][$locale]) : esc_html__('Reset', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                    </span>
                    <span class="iwptp-nm-apply">
                        <?php echo (!empty($strings['apply']) && !empty($strings['apply'][$locale])) ?  esc_html($strings['apply'][$locale]) : esc_html__('Apply', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                    </span>
                </div>
            </div>
            <div class="iwptp-navigation iwptp-left-sidebar">
                <div class="iwptp-nm-filters">
                    <span class="iwptp-nm-filters-placeholder"></span>
                </div>
                <div class="iwptp-nm-sort">
                    <span class="iwptp-nm-sort-placeholder"></span>
                </div>
            </div>
        </div>
    </div>
</div>