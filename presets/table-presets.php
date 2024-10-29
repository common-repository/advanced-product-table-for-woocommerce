<?php

function iwptp_get_default_table_presets()
{
    return [
        'audio-1' => [
            'name' => 'Audio 1',
            'image_url' => IWPTPL_IMAGES_URL . 'table-presets/audio-1.jpg',
            'data' => '',
        ],
        'coffee' => [
            'name' => 'Coffee',
            'image_url' => IWPTPL_IMAGES_URL . 'table-presets/coffee.jpg',
            'data' => '',
        ],
        'domain' => [
            'name' => 'Domain',
            'image_url' => IWPTPL_IMAGES_URL . 'table-presets/domain.jpg',
            'data' => '',
        ],
        'electronics' => [
            'name' => 'Electronics',
            'image_url' => IWPTPL_IMAGES_URL . 'table-presets/electronics.jpg',
            'data' => '',
        ],
        'food-1' => [
            'name' => 'Food 1',
            'image_url' => IWPTPL_IMAGES_URL . 'table-presets/food-1.jpg',
            'data' => '',
        ],
        'food-2' => [
            'name' => 'Food 2',
            'image_url' => IWPTPL_IMAGES_URL . 'table-presets/food-2.jpg',
            'data' => '',
        ],
        'gift' => [
            'name' => 'Gift',
            'image_url' => IWPTPL_IMAGES_URL . 'table-presets/gift.jpg',
            'data' => '',
        ],
        'live-totaling' => [
            'name' => 'Live Totaling',
            'image_url' => IWPTPL_IMAGES_URL . 'table-presets/live-totaling.jpg',
            'data' => '',
        ],
        'live-view' => [
            'name' => 'Live View',
            'image_url' => IWPTPL_IMAGES_URL . 'table-presets/live-view.jpg',
            'data' => '',
        ],
        'pricing-table' => [
            'name' => 'Pricing Table',
            'image_url' => IWPTPL_IMAGES_URL . 'table-presets/pricing-table.jpg',
            'data' => '',
        ],
        'restaurant' => [
            'name' => 'Restaurant',
            'image_url' => IWPTPL_IMAGES_URL . 'table-presets/restaurant.jpg',
            'data' => '',
        ],
        'rtl-table' => [
            'name' => 'RTL Table',
            'image_url' => IWPTPL_IMAGES_URL . 'table-presets/rtl-table.jpg',
            'data' => '',
        ],
        'spare-parts-1' => [
            'name' => 'Spare Parts 1',
            'image_url' => IWPTPL_IMAGES_URL . 'table-presets/spare-parts-1.jpg',
            'data' => '',
        ],
        'spare-parts-2' => [
            'name' => 'Spare Parts 2',
            'image_url' => IWPTPL_IMAGES_URL . 'table-presets/spare-parts-2.jpg',
            'data' => '',
        ],
        'standard-table' => [
            'name' => 'Standard Table',
            'image_url' => IWPTPL_IMAGES_URL . 'table-presets/standard-table.jpg',
            'data' => '',
        ],
        'wine-1' => [
            'name' => 'Wine 1',
            'image_url' => IWPTPL_IMAGES_URL . 'table-presets/wine-1.jpg',
            'data' => '',
        ],
        'wine-2' => [
            'name' => 'Wine 2',
            'image_url' => IWPTPL_IMAGES_URL . 'table-presets/wine-2.jpg',
            'data' => '',
        ],
    ];
}

function iwptp_get_table_presets()
{
    $presets = iwptp_get_default_table_presets();
    $user_presets = get_option(IWPTPL_TABLE_PRESETS_OPTION, []);

    if (!empty($user_presets)) {
        $presets = array_merge($presets, $user_presets);
    }

    return $presets;
}


function iwptp_get_table_preset($preset_slug)
{
    $presets = iwptp_get_table_presets();
    return (!empty($presets[sanitize_text_field($preset_slug)])) ? $presets[sanitize_text_field($preset_slug)] : null;
}

function iwptp_create_table_preset($preset)
{
    if (empty($preset['slug']) || empty($preset['name']) || empty($preset['data']) || empty($preset['image_url'])) {
        return false;
    }
    $presets = get_option(IWPTPL_TABLE_PRESETS_OPTION, []);

    if (isset($presets[sanitize_text_field($preset['slug'])])) {
        $preset['slug'] = $preset['slug'] . wp_rand(100, 999);
    }

    $presets[sanitize_text_field($preset['slug'])] = [
        'name' => sanitize_text_field($preset['name']),
        'image_url' => esc_url($preset['image_url']),
        'deletable' => (isset($preset['deletable']) && $preset['deletable']),
        'data' => iwptp_sanitize_array(json_decode($preset['data'], true))
    ];

    return update_option(IWPTPL_TABLE_PRESETS_OPTION, $presets);
}

function iwptp_delete_table_preset($preset_slug)
{
    if (empty($preset_slug)) {
        return false;
    }

    $presets = get_option(IWPTPL_TABLE_PRESETS_OPTION, []);

    if (!isset($presets[sanitize_text_field($preset_slug)])) {
        return true;
    }

    unset($presets[sanitize_text_field($preset_slug)]);
    return update_option(IWPTPL_TABLE_PRESETS_OPTION, $presets);
}
