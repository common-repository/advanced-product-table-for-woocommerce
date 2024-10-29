<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

$object_id = $product->get_id();

// product variation
if (
    $product->get_type() == 'variation' &&
    !get_post_thumbnail_id($product->get_id())
) {
    $object_id = $product->get_parent_id();
}

$post_thumbnail_id = get_post_thumbnail_id($object_id);

if (empty($click_action)) {
    $click_action = false;
}

if (empty($size)) {
    $size = 'thumbnail';
}

$img_markup = '';

if (
    !$post_thumbnail_id &&
    empty($placeholder_enabled)
) {
    return;
}

if (
    !$post_thumbnail_id &&
    !empty($placeholder_enabled)
) {
    $img_markup = str_replace('class="woocommerce-placeholder', 'class="woocommerce-placeholder ' . $html_class, wc_placeholder_img($size));
    $using_placeholder = true;
} else {
    $img_markup = get_the_post_thumbnail($object_id, $size, array('title' => get_post_field('post_title', $post_thumbnail_id), 'class' => $html_class));
}

$title = '';
$html_class = 'iwptp-product-image-wrapper ' . $html_class;

$lightbox_attrs = '';
$lightbox_icon = '';
if (
    $click_action == 'lightbox' &&
    empty($using_placeholder)
) {
    $lightbox_attrs = ' data-iwptp-lightbox="' . get_the_post_thumbnail_url($object_id, 'large') . '" ';
    $html_class .= ' iwptp-lightbox-enabled ';
    if (empty($icon_when)) {
        $icon_when = 'always';
    }
    if ('never' != $icon_when) {
        $lightbox_icon = iwptp_icon('search', 'iwptp-lightbox-icon iwptp-when-' . $icon_when);
    }
}

$zoom_attrs = '';
if (!empty($zoom_trigger)) {
    $html_class .= ' iwptp-zoom-enabled ';
    if (empty($zoom_scale)) {
        $zoom_scale = '1.75';
    }

    if ($zoom_scale == 'custom') {
        if (empty($custom_zoom_scale)) {
            $custom_zoom_scale = '1.75';
        }

        $zoom_scale = $custom_zoom_scale;
    }

    $zoom_attrs .= ' data-iwptp-zoom-level="' . $zoom_scale . '" ';
    $zoom_attrs .= ' data-iwptp-zoom-trigger="' . $zoom_trigger . '" ';
}

if (empty($icon_position)) {
    $icon_position = 'bottom_right';
}

$gallery_image_ids = $product->get_gallery_image_ids();

// photoswipe
$pswp_ops = '';
$pswp_items = '';
if ($click_action == 'lightbox') {
    // -- option
    $pswp_ops = esc_attr(wp_json_encode(apply_filters(
        'woocommerce_single_product_photoswipe_options',
        array(
            'shareEl' => false,
            'closeOnScroll' => false,
            'history' => false,
            'hideAnimationDuration' => 0,
            'showAnimationDuration' => 0,
        )
    )));

    // -- items
    $items = [];
    $full_size = apply_filters('woocommerce_gallery_full_size', apply_filters('woocommerce_product_thumbnails_large_size', 'full'));
    $images = [];

    if ($featured_image_id = get_post_thumbnail_id($product->get_id())) {
        $images[] = $featured_image_id;
    }

    if ($gallery_image_ids) {
        $images = array_merge($images, $gallery_image_ids);
    }

    foreach ($images as $attachment_id) {
        $full_src = wp_get_attachment_image_src($attachment_id, $full_size);

        if (!empty($full_src)) {
            $items[] = array(
                'src' => $full_src[0],
                'w' => $full_src[1],
                'h' => $full_src[2],
                'title' => _wp_specialchars(get_post_field('post_title', $attachment_id), ENT_QUOTES, 'UTF-8', true),
            );
        }
    }
    $pswp_items = esc_attr(wp_json_encode($items));
}

$html_class .= ' iwptp-lightbox-icon-position-' . $icon_position;

// hover switch
if (
    !empty($hover_switch_enabled) &&
    !empty($gallery_image_ids)
) {
    $hover_image_arr = wp_get_attachment_image_src($gallery_image_ids[0], $size);
    if (!empty($hover_image_arr)) {
        $src = $hover_image_arr[0];
        $img_markup .= '<img src="' . $hover_image_arr[0] . '" class="iwptp-product-image-on-hover">';
        $html_class .= ' iwptp-product-image-hover-switch-enabled ';
    }
}

if (!empty($image_count_enabled) && $gallery_image_ids) {
    $image_count_num = count($gallery_image_ids);

    if (!empty($featured_image_id) && !in_array($featured_image_id, $gallery_image_ids)) {
        ++$image_count_num;
    }

    $image_count = '<span class="iwptp-image-count"><span>' . ($image_count_num) . '</span>' . iwptp_get_icon('image') . '</span>';
    $html_class .= ' iwptp-image-count-enabled ';
} else {
    $image_count = '';
}

if (
    !empty($image_count_enabled) ||
    $click_action === 'lightbox'
) {
    $append_html_class = ' iwptp-product-image-wrapper--default-width-60px ';

    if (
        !empty($image_count_enabled) &&
        $click_action === 'lightbox'
    ) {
        $append_html_class = ' iwptp-product-image-wrapper--default-width-80px ';
    }

    $html_class .= $append_html_class;
}

$offset_zoom_attrs = '';

if (!empty($offset_zoom_enabled) && empty($using_placeholder)) {
    $offset_zoom_attrs = ' data-iwptp-offset-zoom-image-src="' . get_the_post_thumbnail_url($object_id, 'large') . '" ';
    $offset_zoom_attrs .= ' data-iwptp-offset-zoom-image-html-class="iwptp-' . $id . '--offset-zoom-image" ';

    $html_class .= ' iwptp-product-image-wrapper--offset-zoom-enabled ';
}

$tag = 'div';
$target = '';
$href = '';

if (in_array($click_action, array('product_page', 'product_page_new', 'image_page_new'))) {
    $tag = 'a';

    if (in_array($click_action, array('product_page_new', 'image_page_new'))) {
        $target = ' target="_blank" ';
    }

    $href = ' href="' . ($click_action == 'image_page_new' ? get_the_post_thumbnail_url($object_id) : get_the_permalink($product->get_id())) . '" ';
}

echo '<' . esc_attr($tag) . '
class="' . esc_attr($html_class) . '"
data-iwptp-image-size="' . esc_attr($size) . '"
data-iwptp-photoswipe-options="' . esc_attr($pswp_ops) . '"
data-iwptp-photoswipe-items="' . esc_attr($pswp_items) . '"
' . esc_attr($target) . '
' . esc_url($href) . '
' . esc_attr($lightbox_attrs) . '
' . esc_attr($zoom_attrs) . '
' . esc_attr($offset_zoom_attrs) . '
>' . wp_kses($img_markup . $lightbox_icon . $image_count, iwptp_allowed_html_tags()) . '</' . esc_attr($tag) . '>';
