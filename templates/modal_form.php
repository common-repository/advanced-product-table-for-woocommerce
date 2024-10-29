<div class="iwptp-product-form iwptp-modal" data-iwptp-product-id="<?php echo esc_attr($product_id); ?>">
    <div class="iwptp-modal-content">
        <div class="iwptp-close-modal">
            <?php echo wp_kses(iwptp_icon('x', 'iwptp-close-modal-icon'), iwptp_allowed_html_tags()); ?>
        </div>
        <span class="iwptp-product-form-title">
            <?php echo esc_html($product->get_title()); ?>
            <span class="iwptp-product-form-price"><?php echo wp_kses($product->get_price_html(), iwptp_allowed_html_tags()); ?></span>
        </span>
        <?php do_action('iwptp_modal_form_content_start'); ?>
        <?php do_action('woocommerce_' . esc_attr($product_type) . '_add_to_cart'); ?>
    </div>
</div>