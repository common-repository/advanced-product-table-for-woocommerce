<?php

function iwptp_get_element_settings_header($title)
{
    return '<h2 class="iwptp-element-settings-title">
            <a href="javascript:;" class="iwptp-block-editor-lightbox-close" title="Back">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
            </a>
            <span>' . esc_html($title) . '</span>
        </h2>';
}

function iwptp_get_default_table_data()
{
    return [
        'query' => array(
            'include_products' => [],
            'exclude_products' => [],
            'include_taxonomies' => [],
            'exclude_taxonomies' => [],
            'product_statuses' => [],
            'price_from' => '',
            'price_to' => '',
            'orderby' => 'price',
            'order' => 'ASC',
            'limit' => 10,
            'offset' => 0,
            'hide_out_of_stock_items' => '',
            'include_product_types' => [],
            'exclude_product_types' => [],
            'include_users' => [],
            'include_skus' => '',
            'exclude_skus' => '',
            'visibility' => 'visible',
        ),
        'columns' => array(
            'laptop' => [],
            'tablet' => [],
            'phone' => [],
        ),
        'navigation' => array(
            'laptop' => array(
                'header' => array(
                    'rows' => array(
                        array(
                            'columns_enabled' => 'left-right',
                            'columns' => array(
                                'left' => array(
                                    'template' => '',
                                ),
                                'right' => array(
                                    'template' => '',
                                ),
                                'center' => array(
                                    'template' => '',
                                ),
                            ),
                        ),
                    ),
                ),
                'footer' => array(
                    'rows' => array(
                        array(
                            'columns_enabled' => 'left-right',
                            'columns' => array(
                                'left' => array(
                                    'template' => '',
                                ),
                                'right' => array(
                                    'template' => '',
                                ),
                                'center' => array(
                                    'template' => '',
                                ),
                            ),
                        ),
                    ),
                ),
                'left_sidebar' => false,
            ),
            'tablet' => false,
            'phone' => false,
        ),
        'style' => array(
            'css' => '',
            'laptop' => [
                "[container]" => [
                    "direction" => "ltr",
                ],
                "[container] .iwptp-table" => [
                    "border-bottom-width" => "",
                    "border-bottom-color" => "",
                ],
                "[container] .iwptp-heading-row" => [
                    "border-bottom-width" => "",
                    "border-bottom-color" => "",
                ],
                "[container] .iwptp-cell" => [
                    "font-family" => "",
                ],
            ],
            'tablet' => array(
                'inherit_laptop_style' => true,
                "[container] .iwptp-table" => [
                    "border-bottom-width" => "",
                    "border-bottom-color" => "",
                ],
                "[container] .iwptp-heading-row" => [
                    "border-bottom-width" => "",
                    "border-bottom-color" => "",
                ],
                "[container] .iwptp-cell" => [
                    "font-family" => "",
                ],
            ),
            'phone' => array(
                'inherit_tablet_style' => false,
                "[container] .iwptp-table" => [
                    "border-bottom-width" => "",
                    "border-bottom-color" => "",
                ],
                "[container] .iwptp-heading-row" => [
                    "border-bottom-width" => "",
                    "border-bottom-color" => "",
                ],
                "[container] .iwptp-cell" => [
                    "font-family" => "",
                ],
            ),
            'navigation' => [],
            'mini_cart' => [],
            'checkbox_trigger' => [],
        ),
        'elements' => array(
            'column' => [],
            'navigation' => [],
        ),
        'version' => IWPTPL_VERSION,
        'timestamp' => time(),
    ];
}

function iwptp_allowed_html_tags()
{
    $allowed = wp_kses_allowed_html('post');

    $allowed['input']['data-*'] = true;
    $allowed['input']['checked'] = true;
    $allowed['input']['disabled'] = true;
    $allowed['input']['type'] = true;
    $allowed['input']['id'] = true;
    $allowed['input']['class'] = true;
    $allowed['input']['placeholder'] = true;
    $allowed['input']['style'] = true;
    $allowed['input']['value'] = true;
    $allowed['input']['name'] = true;

    $allowed['label']['data-*'] = true;
    $allowed['label']['class'] = true;
    $allowed['label']['id'] = true;
    $allowed['label']['for'] = true;
    $allowed['label']['style'] = true;

    $allowed['option']['data-*'] = true;
    $allowed['option']['value'] = true;
    $allowed['option']['selected'] = true;
    $allowed['option']['disabled'] = true;

    $allowed['span']['data-*'] = true;
    $allowed['span']['class'] = true;
    $allowed['span']['style'] = true;
    $allowed['span']['id'] = true;

    $allowed['li']['data-*'] = true;
    $allowed['li']['class'] = true;
    $allowed['li']['style'] = true;
    $allowed['li']['id'] = true;

    $allowed['ul']['data-*'] = true;
    $allowed['ul']['class'] = true;
    $allowed['ul']['style'] = true;
    $allowed['ul']['id'] = true;

    $allowed['img']['data-*'] = true;
    $allowed['img']['class'] = true;
    $allowed['img']['style'] = true;
    $allowed['img']['src'] = true;
    $allowed['img']['href'] = true;
    $allowed['img']['id'] = true;

    $allowed['i']['data-*'] = true;
    $allowed['i']['class'] = true;
    $allowed['i']['style'] = true;
    $allowed['i']['id'] = true;

    $allowed['select']['data-*'] = true;
    $allowed['select']['class'] = true;
    $allowed['select']['name'] = true;
    $allowed['select']['id'] = true;
    $allowed['select']['disabled'] = true;
    $allowed['select']['multiple'] = true;
    $allowed['select']['style'] = true;
    $allowed['select']['title'] = true;

    $allowed['button']['data-*'] = true;
    $allowed['button']['class'] = true;
    $allowed['button']['type'] = true;
    $allowed['button']['name'] = true;
    $allowed['button']['id'] = true;
    $allowed['button']['disabled'] = true;
    $allowed['button']['style'] = true;
    $allowed['button']['title'] = true;

    $allowed['textarea']['data-*'] = true;
    $allowed['textarea']['title'] = true;
    $allowed['textarea']['placeholder'] = true;
    $allowed['textarea']['name'] = true;
    $allowed['textarea']['disabled'] = true;

    $allowed['div']['style'] = true;
    $allowed['div']['data-*'] = true;
    $allowed['div']['class'] = true;
    $allowed['div']['id'] = true;

    $allowed['a']['data-*'] = true;
    $allowed['a']['class'] = true;
    $allowed['a']['style'] = true;
    $allowed['a']['id'] = true;
    $allowed['a']['href'] = true;
    $allowed['a']['target'] = true;

    $allowed['table']['style'] = true;
    $allowed['table']['data-*'] = true;
    $allowed['table']['class'] = true;
    $allowed['table']['id'] = true;

    $allowed['thead']['style'] = true;
    $allowed['thead']['data-*'] = true;
    $allowed['thead']['class'] = true;
    $allowed['thead']['id'] = true;

    $allowed['tbody']['style'] = true;
    $allowed['tbody']['data-*'] = true;
    $allowed['tbody']['class'] = true;
    $allowed['tbody']['id'] = true;

    $allowed['tr']['style'] = true;
    $allowed['tr']['data-*'] = true;
    $allowed['tr']['class'] = true;
    $allowed['tr']['id'] = true;

    $allowed['th']['style'] = true;
    $allowed['th']['data-*'] = true;
    $allowed['th']['class'] = true;
    $allowed['th']['id'] = true;

    $allowed['td']['style'] = true;
    $allowed['td']['data-*'] = true;
    $allowed['td']['class'] = true;
    $allowed['td']['id'] = true;

    $allowed['style']['data-*'] = true;
    $allowed['style']['id'] = true;

    $allowed['script']['data-*'] = true;
    $allowed['script']['id'] = true;

    $allowed['style'] = [];
    $allowed['form']['action'] = true;
    $allowed['form']['method'] = true;

    return $allowed;
}
