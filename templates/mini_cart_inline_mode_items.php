<?php
$cart_items = WC()->cart->get_cart();
if (!empty($cart_items)) :
    foreach ($cart_items as $item_key => $cart_item) :
        $product = $cart_item['data'];
        if ($product instanceof \WC_Product) :
?>
            <div class="iwptp-mini-cart-item" data-key="<?php echo esc_attr($item_key); ?>">
                <div class="iwptp-mini-cart-item-image">
                    <?php echo wp_kses($product->get_image([40, 40]), iwptp_allowed_html_tags()); ?>
                </div>
                <div class="iwptp-mini-cart-item-info">
                    <div><strong><?php echo esc_html($product->get_title()); ?></strong></div>
                    <div class="iwptp-mini-cart-meta"><?php echo esc_html($cart_item['quantity']) . ' x ' . wp_kses(WC()->cart->get_product_price($product), iwptp_allowed_html_tags()); ?></div>
                </div>
                <button type="button" title="Delete" value="<?php echo esc_attr($item_key); ?>" class="iwptp-mini-cart-item-delete iwptp-mini-cart-meta"><i class="dashicons dashicons-no-alt"></i></button>
            </div>
<?php
        endif;
    endforeach;
else :
    echo 'Your cart is currently empty';
endif;
