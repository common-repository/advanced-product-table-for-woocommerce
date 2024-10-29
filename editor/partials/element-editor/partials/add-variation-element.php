<?php

echo wp_kses(iwptp_get_element_settings_header(__('Add Price Element', 'ithemeland-woocommerce-product-table-pro-lite')), iwptp_allowed_html_tags());

iwptp_elm_type_list(array(
    [
        'label' => 'Text',
        'type' => 'lite'
    ],
    [
        'label' => 'Price__Variation',
        'type' => 'pro'
    ],
    [
        'label' => 'Select__Variation',
        'type' => 'pro'
    ],
    [
        'label' => 'Availability',
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
