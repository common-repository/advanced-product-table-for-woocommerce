<?php

echo wp_kses(iwptp_get_element_settings_header(__('Add Price Sale Element', 'ithemeland-woocommerce-product-table-pro-lite')), iwptp_allowed_html_tags());

iwptp_elm_type_list(array(
    [
        'label' => 'Sale price',
        'type' => 'pro'
    ],
    [
        'label' => 'Regular price__On sale',
        'type' => 'pro'
    ],
    [
        'label' => 'Lowest price',
        'type' => 'pro'
    ],
    [
        'label' => 'Highest price',
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
));
