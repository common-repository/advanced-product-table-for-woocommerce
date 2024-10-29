<?php

echo wp_kses(iwptp_get_element_settings_header(__('Add Element', 'ithemeland-woocommerce-product-table-pro-lite')), iwptp_allowed_html_tags());

iwptp_elm_type_list(array(
    [
        'label' => 'Text',
        'type' => 'lite'
    ],
    [
        'label' => 'HTML',
        'type' => 'pro'
    ],
    [
        'label' => 'Space',
        'type' => 'pro'
    ],
    [
        'label' => 'Dot',
        'type' => 'pro'
    ],
    [
        'label' => 'Icon',
        'type' => 'pro'
    ],
    [
        'label' => 'Media image',
        'type' => 'pro'
    ],
    [
        'label' => 'Custom Field',
        'type' => 'pro'
    ],
    [
        'label' => 'Price',
        'type' => 'pro'
    ],
    [
        'label' => 'SKU',
        'type' => 'pro'
    ],
));