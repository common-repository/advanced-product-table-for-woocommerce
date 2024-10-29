<?php

echo wp_kses(iwptp_get_element_settings_header(__('Add Element', 'ithemeland-woocommerce-product-table-pro-lite')), iwptp_allowed_html_tags());

iwptp_elm_type_list(array(
    [
        'label' => 'Text__Col',
        'type' => 'lite'
    ],
    [
        'label' => 'HTML__Col',
        'type' => 'pro'
    ],
    [
        'label' => 'Dot__Col',
        'type' => 'pro'
    ],
    [
        'label' => 'Space__Col',
        'type' => 'pro'
    ],
    [
        'label' => 'Tooltip',
        'type' => 'pro'
    ],
    [
        'label' => 'Icon__Col',
        'type' => 'pro'
    ],
    [
        'label' => 'Media Image__Col',
        'type' => 'pro'
    ],
));
