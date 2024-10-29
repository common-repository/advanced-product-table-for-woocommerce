<?php
$bottom_offset = (isset($bottom_offset)) ? intval($bottom_offset) : 50;
$width = (isset($width)) ? intval($width) : 400;

?>
<div class="iwptp-cart-widget <?php echo $total_qty ? '' : 'iwptp-hide'; ?>" data-iwptp-href="<?php echo esc_url($link_url); ?>">
    <style media="screen">
        @media(min-width:1200px) {
            .iwptp-cart-widget {
                width: <?php echo intval($width); ?>px !important;
                display: <?php echo (isset($toggle) && $toggle == 'enabled') ? 'inline-block' : 'none'; ?>;
                bottom: <?php echo intval($bottom_offset); ?>px;
            }
        }

        @media(max-width:1199px) {
            .iwptp-cart-widget {
                display: <?php echo (isset($r_toggle) && $r_toggle == 'enabled') ? 'inline-block' : 'none'; ?>;
            }
        }
    </style>
    <div class="iwptp-cw-half">
        <!-- top -->
        <span class="iwptp-cw-qty-total iwptp-mini-cart-meta">
            <span class="iwptp-cw-figure"><?php echo esc_html($total_qty); ?></span>
            <span class="iwptp-cw-text">
                <?php
                if ($total_qty > 1) {
                    echo (!empty($strings['items']) && !empty($strings['items'][$locale])) ? esc_html($strings['items'][$locale]) : esc_html__('Items', 'ithemeland-woocommerce-product-table-pro-lite');
                } else {
                    echo (!empty($strings['item']) && !empty($strings['item'][$locale])) ? esc_html($strings['item'][$locale]) : esc_html__('Item', 'ithemeland-woocommerce-product-table-pro-lite');
                }
                ?>
            </span>
        </span>
        <span class="iwptp-cw-separator iwptp-mini-cart-meta">|</span>
        <span class="iwptp-cw-price-total iwptp-mini-cart-meta">
            <?php echo esc_html($total_price); ?>
        </span>
        <!-- bottom -->
        <?php
        if (!empty($strings['extra_charges']) && !empty($strings['extra_charges'][$locale])) {
            echo '<div class="iwptp-cw-footer iwptp-mini-cart-meta">';
            echo esc_html($strings['extra_charges'][$locale]);
            echo '</div>';
        }
        ?>
    </div>
    <a href="<?php echo esc_url(apply_filters('iwptp_cart_widget_url', $link_url)); ?>" class="iwptp-cw-half">
        <span class="iwptp-cw-loading-icon"><?php echo wp_kses(iwptp_icon('loader'), iwptp_allowed_html_tags()); ?></span>
        <div class="iwptp-min-cart-default-button">
            <span class="iwptp-cw-view-label">
                <?php
                if (!empty($strings['view_cart']) && !empty($strings['view_cart'][$locale])) {
                    echo esc_html($strings['view_cart'][$locale]);
                } else {
                    esc_html_e('View Cart', 'woocommerce');
                }
                ?>
            </span>
            <span class="iwptp-cw-cart-icon">
                <?php
                ob_start();
                echo wp_kses(iwptp_icon('shopping-bag'), iwptp_allowed_html_tags());
                echo wp_kses(apply_filters('iwptp_cart_widget_icon_markup', ob_get_clean()), iwptp_allowed_html_tags());
                ?>
            </span>
        </div>
    </a>
</div>