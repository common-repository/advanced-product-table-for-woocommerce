<div class="iwptp-mini-cart iwptp-hide iwptp-mini-cart-inline-mode-container <?php echo (!empty($html_class)) ? esc_sql($html_class) : ''; ?>" data-settings="<?php echo esc_attr($mini_cart_settings); ?>">
    <div class="iwptp-mini-cart-items">
        <?php include "mini_cart_inline_mode_items.php"; ?>
    </div>

    <div class="iwptp-mini-cart-inline-mode-bottom">
        <div class="iwptp-mini-cart-inline-mode-bottom-left">
            <?php if (isset($mini_cart_subtotal) && $mini_cart_subtotal == 'enable') : ?>
                <div class="iwptp-mini-cart-meta"><strong>Subtotal: <span class="iwptp-mini-cart-subtotal"><?php echo wp_kses($total_price, iwptp_allowed_html_tags()); ?></span></strong></div>
                <div class="iwptp-mini-cart-meta"><span class="iwptp-mini-cart-total-quantity"><?php echo ($total_qty) ? esc_html($total_qty) : '0'; ?> <?php echo ($total_qty && $total_qty > 1) ? 'Items' : 'item'; ?></span> in cart</div>
            <?php endif; ?>
        </div>
        <div class="iwptp-mini-cart-inline-mode-buttons">
            <?php if (!empty($empty_cart_button) && $empty_cart_button == 'enable') : ?>
                <button type="button" class="iwptp-mini-clear-cart-button"><i class="dashicons dashicons-trash"></i> Clear Cart</button>
            <?php endif; ?>

            <?php if (!empty($view_checkout_button) && $view_checkout_button == 'enable') : ?>
                <a href="<?php echo esc_url(wc_get_checkout_url()); ?>"><i class="dashicons dashicons-cart"></i> Checkout</a>
            <?php endif; ?>

            <?php if (!empty($view_cart_button) && $view_cart_button == 'enable') : ?>
                <a href="<?php echo esc_url(wc_get_cart_url()); ?>"><i class="dashicons dashicons-visibility"></i> View Cart</a>
            <?php endif; ?>
        </div>
    </div>
</div>