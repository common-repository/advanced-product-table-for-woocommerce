<?php

echo wp_kses(iwptp_get_element_settings_header(__('Add Navigation Element', 'ithemeland-woocommerce-product-table-pro-lite')), iwptp_allowed_html_tags());

iwptp_elm_type_list(array(
    [
        'label' => 'Pagination',
        'type' => 'lite'
    ],
    [
        'label' => 'Full screen',
        'type' => 'pro'
    ],
    [
        'label' => 'Date range',
        'type' => 'pro'
    ],
    [
        'label' => 'Mini cart',
        'type' => 'lite'
    ],
    [
        'label' => 'Sort By',
        'type' => 'lite'
    ],
    [
        'label' => 'Result Count',
        'type' => 'pro'
    ],
    [
        'label' => 'Results per page',
        'type' => 'lite'
    ],
    [
        'label' => 'Category Filter',
        'type' => 'lite'
    ],
    [
        'label' => 'Clear Filters',
        'type' => 'lite'
    ],
    [
        'label' => 'Price Filter',
        'type' => 'pro'
    ],
    [
        'label' => 'Search',
        'type' => 'pro'
    ],
    [
        'label' => 'Apply / Reset',
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
        'label' => 'Icon',
        'type' => 'pro'
    ],
    [
        'label' => 'Media Image',
        'type' => 'pro'
    ],
    [
        'label' => 'Tags Filter',
        'type' => 'pro'
    ],
    [
        'label' => 'Attribute Filter',
        'type' => 'pro'
    ],
    [
        'label' => 'Custom Field Filter',
        'type' => 'pro'
    ],
    [
        'label' => 'Taxonomy Filter',
        'type' => 'pro'
    ],
    [
        'label' => 'Availability Filter',
        'type' => 'pro'
    ],
    [
        'label' => 'On Sale Filter',
        'type' => 'pro'
    ],
    [
        'label' => 'Rating Filter',
        'type' => 'pro'
    ],
    [
        'label' => 'Add Selected To Cart',
        'type' => 'pro'
    ],
    [
        'label' => 'Download CSV',
        'type' => 'pro'
    ],
));
