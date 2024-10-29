<?php

function iwptp_get_default_style_presets()
{
    return [
        'template-blue' => [
            'name' => 'Template - Blue',
            'image_url' => IWPTPL_IMAGES_URL . 'style-presets/template-blue.jpg',
            'data' => '',
        ],
        'template-green' => [
            'name' => 'Template - Green',
            'image_url' => IWPTPL_IMAGES_URL . 'style-presets/template-green.jpg',
            'data' => '',
        ],
        'template-purple' => [
            'name' => 'Template - Purple',
            'image_url' => IWPTPL_IMAGES_URL . 'style-presets/template-purple.jpg',
            'data' => '',
        ],
        'template-dark' => [
            'name' => 'Template - Dark',
            'image_url' => IWPTPL_IMAGES_URL . 'style-presets/template-dark.jpg',
            'data' => json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR  . 'style' . DIRECTORY_SEPARATOR . 'dark.json'), true) // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents 
        ],
    ];
}

function iwptp_get_style_presets()
{
    $presets = iwptp_get_default_style_presets();
    $user_presets = get_option(IWPTPL_STYLE_PRESETS_OPTION, []);

    if (!empty($user_presets)) {
        $presets = array_merge($presets, $user_presets);
    }

    return $presets;
}

function iwptp_get_style_preset($preset_slug)
{
    $presets = iwptp_get_style_presets();
    return (!empty($presets[sanitize_text_field($preset_slug)])) ? $presets[sanitize_text_field($preset_slug)] : null;
}

function iwptp_create_style_preset($preset)
{
    if (empty($preset['slug']) || empty($preset['name']) || empty($preset['data']) || empty($preset['image_url'])) {
        return false;
    }
    $presets = get_option(IWPTPL_STYLE_PRESETS_OPTION, []);

    if (isset($presets[sanitize_text_field($preset['slug'])])) {
        $preset['slug'] = $preset['slug'] . wp_rand(100, 999);
    }

    $presets[sanitize_text_field($preset['slug'])] = [
        'name' => sanitize_text_field($preset['name']),
        'image_url' => esc_url($preset['image_url']),
        'deletable' => (isset($preset['deletable']) && $preset['deletable']),
        'data' => iwptp_sanitize_array(json_decode($preset['data'], true))
    ];

    return update_option(IWPTPL_STYLE_PRESETS_OPTION, $presets);
}

function iwptp_delete_style_preset($preset_slug)
{
    if (empty($preset_slug)) {
        return false;
    }

    $presets = get_option(IWPTPL_STYLE_PRESETS_OPTION, []);

    if (!isset($presets[sanitize_text_field($preset_slug)])) {
        return true;
    }

    unset($presets[sanitize_text_field($preset_slug)]);
    return update_option(IWPTPL_STYLE_PRESETS_OPTION, $presets);
}
