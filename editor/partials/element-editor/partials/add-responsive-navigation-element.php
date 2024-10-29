<?php

echo wp_kses(iwptp_get_element_settings_header(__('Add Responsive Navigation Element', 'ithemeland-woocommerce-product-table-pro-lite')), iwptp_allowed_html_tags());

iwptp_elm_type_list(array(
    [
        'label' => 'Result Count',
        'type' => 'pro'
    ],
    [
        'label' => 'Filter Modal',
        'type' => 'pro'
    ],
    [
        'label' => 'Sort Modal',
        'type' => 'pro'
    ],
    [
        'label' => 'Full Screen',
        'type' => 'pro'
    ],
    [
        'label' => 'Clear Filters',
        'type' => 'pro'
    ],
    [
        'label' => 'Text',
        'type' => 'lite'
    ],
    [
        'label' => 'HTML',
        'type' => 'pro'
    ],
    [
        'label' => 'Icon',
        'type' => 'pro'
    ],
));
