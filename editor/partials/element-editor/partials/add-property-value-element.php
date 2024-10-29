<?php

echo wp_kses(iwptp_get_element_settings_header(__('Add Property Value Element', 'ithemeland-woocommerce-product-table-pro-lite')), iwptp_allowed_html_tags());

iwptp_elm_type_list(array(
    [
        'label' => 'Attribute',
        'type' => 'pro'
    ],
    [
        'label' => 'Custom Field',
        'type' => 'pro'
    ],
    [
        'label' => 'Price',
        'type' => 'lite'
    ],
    [
        'label' => 'On Sale',
        'type' => 'pro'
    ],
    [
        'label' => 'Rating',
        'type' => 'pro'
    ],
    [
        'label' => 'Availability',
        'type' => 'pro'
    ],
    [
        'label' => 'Category',
        'type' => 'lite'
    ],
    [
        'label' => 'Tags',
        'type' => 'pro'
    ],
    [
        'label' => 'Taxonomy',
        'type' => 'pro'
    ],
    [
        'label' => 'Title',
        'type' => 'lite'
    ],
    [
        'label' => 'Product Image',
        'type' => 'lite'
    ],
    [
        'label' => 'Excerpt',
        'type' => 'pro'
    ],
    [
        'label' => 'Content',
        'type' => 'lite'
    ],
    [
        'label' => 'Quantity',
        'type' => 'lite'
    ],
    [
        'label' => 'Stock',
        'type' => 'pro'
    ],
    [
        'label' => 'SKU',
        'type' => 'lite'
    ],
    [
        'label' => 'Product Link',
        'type' => 'lite'
    ],
    [
        'label' => 'Button',
        'type' => 'lite'
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
