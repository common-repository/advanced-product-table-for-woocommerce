<?php

echo wp_kses(iwptp_get_element_settings_header(__('Add Element To Column Heading', 'ithemeland-woocommerce-product-table-pro-lite')), iwptp_allowed_html_tags());

iwptp_elm_type_list([
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
        'label' => 'ToolTip',
        'type' => 'pro'
    ],
    [
        'label' => 'Sorting',
        'type' => 'lite'
    ],
    [
        'label' => 'Icon',
        'type' => 'pro'
    ],
    [
        'label' => 'Media image',
        'type' => 'pro'
    ],
]);
