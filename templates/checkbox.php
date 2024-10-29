<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

global $product;

if (empty($product) || !in_array($product->get_type(), array('simple', 'variable', 'variation', 'variable-subscription', 'subscription'))) {
    return;
}

$disabled = '';
$title = '';

if (in_array($product->get_type(), array('simple', 'variation')) && !$product->is_in_stock()) {
    $disabled = ' disabled="disabled" ';
    $title = ' title="' . __('Out of stock', 'woocommerce') . '" ';
}

$heading = empty($heading_enabled) ? '' : ' data-iwptp-heading-enabled="true" ';

include(IWPTPL_PLUGIN_PATH . 'templates/checkbox-trigger.php');
?>
<div class="iwptp-cart-checkbox-wrapper">
    <?php echo wp_kses(iwptp_icon('loader'), iwptp_allowed_html_tags()); ?>
    <input type="checkbox" class="iwptp-cart-checkbox <?php echo !empty($refresh_enabled) ? ' iwptp-refresh-enabled ' : ''; ?>" <?php echo esc_attr($heading . $disabled . $title); ?> name="iwptp-cart-checkbox" value="<?php echo intval($product->get_id()); ?>" />
</div>