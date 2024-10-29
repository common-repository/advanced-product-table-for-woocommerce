<div class="iwptp-option-row">
    <div class="iwptp-option-label">
        <?php esc_html_e("Include Products", 'ithemeland-woocommerce-product-table-pro-lite'); ?>:
    </div>
    <div class="iwptp-input">
        <select class="iwptp-products-variations iwptp-select2-products-variations" iwptp-model-key="include_products[]" iwptp-controller="include_products" data-placeholder="Select products ..." multiple>
        </select>
    </div>
</div>

<div class="iwptp-option-row">
    <div class="iwptp-option-label">
        <?php esc_html_e("Exclude Products", 'ithemeland-woocommerce-product-table-pro-lite'); ?>:
    </div>
    <div class="iwptp-input">
        <select iwptp-model-key="exclude_products[]" class="iwptp-select2-products-variations" iwptp-controller="exclude_products" data-placeholder="Select products ..." multiple>
        </select>
    </div>
</div>

<div class="iwptp-option-row">
    <div class="iwptp-option-label">
        <?php esc_html_e("Include Taxonomies", 'ithemeland-woocommerce-product-table-pro-lite'); ?>:
    </div>
    <div class="iwptp-input">
        <select iwptp-model-key="include_taxonomies[]" class="iwptp-select2-taxonomies" iwptp-controller="include_taxonomies" data-placeholder="Select taxonomies ..." multiple>
        </select>
    </div>
</div>

<div class="iwptp-option-row">
    <div class="iwptp-option-label">
        <?php esc_html_e("Exclude Taxonomies", 'ithemeland-woocommerce-product-table-pro-lite'); ?>:
    </div>
    <div class="iwptp-input">
        <select iwptp-model-key="exclude_taxonomies[]" class="iwptp-select2-taxonomies" iwptp-controller="exclude_taxonomies" data-placeholder="Select taxonomies ..." multiple>
        </select>
    </div>
</div>

<!-- product status -->
<div class="iwptp-option-row">
    <div class="iwptp-option-label"><?php esc_html_e("Product status", 'ithemeland-woocommerce-product-table-pro-lite'); ?>:</div>
    <div class="iwptp-input">
        <select iwptp-model-key="product_statuses" class="iwptp-select2 iwptp-query-product-status" data-placeholder="Select statuses ..." multiple>
            <option value="featured"><?php esc_html_e('Featured', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="on_sale"><?php esc_html_e('On sale', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="only_in_stock"><?php esc_html_e('Only in stock', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="only_out_of_stock"><?php esc_html_e('Only out of stock', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="hidden_products"><?php esc_html_e('Hidden products', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="private_products"><?php esc_html_e('Private products', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
        </select>
    </div>
</div>

<!-- price from -->
<div class="iwptp-option-row">
    <div class="iwptp-option-label"><?php esc_html_e("Price from", 'ithemeland-woocommerce-product-table-pro-lite'); ?>:</div>
    <div class="iwptp-input">
        <input class="iwptp-price-from" iwptp-model-key="price_from" type="number" min="-1" value="" />
    </div>
</div>

<!-- price to -->
<div class="iwptp-option-row">
    <div class="iwptp-option-label"><?php esc_html_e("Price to", 'ithemeland-woocommerce-product-table-pro-lite'); ?>:</div>
    <div class="iwptp-input">
        <input class="iwptp-price-to" iwptp-model-key="price_to" type="number" min="-1" value="" />
    </div>
</div>

<!-- limit -->
<div class="iwptp-option-row">
    <div class="iwptp-option-label"><?php esc_html_e("Max products per page", 'ithemeland-woocommerce-product-table-pro-lite'); ?>:</div>
    <div class="iwptp-input">
        <input class="iwptp-limit" iwptp-model-key="limit" type="number" min="-1" value="" />
    </div>
</div>

<!-- offset -->
<div class="iwptp-option-row">
    <div class="iwptp-option-label"><?php esc_html_e("Offset", 'ithemeland-woocommerce-product-table-pro-lite'); ?>:</div>
    <div class="iwptp-input">
        <input class="iwptp-offset" iwptp-model-key="offset" type="number" min="0" value="" />
    </div>
</div>

<!-- orderby -->
<div class="iwptp-option-row">
    <div class="iwptp-option-label"><?php esc_html_e("Initial orderby", 'ithemeland-woocommerce-product-table-pro-lite'); ?>:</div>
    <div class="iwptp-input">
        <select class="iwptp-orderby" iwptp-model-key="orderby">
            <?php
            $orderby_options  = array(
                'title' => __('Title', 'ithemeland-woocommerce-product-table-pro-lite'),
                'date' => __('Date', 'ithemeland-woocommerce-product-table-pro-lite'),
                'menu_order' => __('Menu order', 'ithemeland-woocommerce-product-table-pro-lite'),
                'rating' => __('Average rating', 'ithemeland-woocommerce-product-table-pro-lite'),
                'price' => __('Price: low to high ', 'ithemeland-woocommerce-product-table-pro-lite'),
                'price-desc' => __('Price: high to low', 'ithemeland-woocommerce-product-table-pro-lite'),
                'popularity' => __('Popularity (sales)', 'ithemeland-woocommerce-product-table-pro-lite'),
                'rand' => __('Random', 'ithemeland-woocommerce-product-table-pro-lite'),
            );
            foreach ($orderby_options as $option => $label) {
            ?>
                <option value="<?php echo esc_attr($option) ?>"><?php echo esc_html($label); ?></option>
            <?php
            }
            ?>
            <?php iwptp_option('meta_value', __('Custom field: as text', 'ithemeland-woocommerce-product-table-pro-lite')); ?>
            <?php iwptp_option('meta_value_num', __('Custom field: as number', 'ithemeland-woocommerce-product-table-pro-lite')); ?>
            <?php iwptp_option('id', __('Product ID', 'ithemeland-woocommerce-product-table-pro-lite')); ?>
            <?php iwptp_option('sku', __('SKU: as text', 'ithemeland-woocommerce-product-table-pro-lite')); ?>
            <?php iwptp_option('sku_num', __('SKU: as number', 'ithemeland-woocommerce-product-table-pro-lite')); ?>
        </select>
    </div>
</div>

<!-- orderby: meta key -->
<div class="iwptp-option-row" iwptp-panel-condition="prop" iwptp-condition-prop="orderby" iwptp-condition-val="meta_value_num||meta_value">
    <div class="iwptp-option-label"><?php esc_html_e("Custom field to orderby", 'ithemeland-woocommerce-product-table-pro-lite'); ?>:</div>
    <div class="iwptp-input">
        <input iwptp-model-key="meta_key" type="text">
    </div>
</div>

<!-- order -->
<div class="iwptp-option-row">
    <div class="iwptp-option-label"><?php esc_html_e("Initial order", 'ithemeland-woocommerce-product-table-pro-lite'); ?>:</div>
    <div class="iwptp-input">
        <select class="iwptp-order" iwptp-model-key="order">
            <?php
            $order_options  = array(
                'ASC' => __('Ascending', 'ithemeland-woocommerce-product-table-pro-lite'),
                'DESC' => __('Descending', 'ithemeland-woocommerce-product-table-pro-lite'),
            );
            foreach ($order_options as $option => $label) {
            ?>
                <option value="<?php echo esc_attr($option) ?>"><?php echo esc_html($label); ?></option>
            <?php
            }
            ?>
        </select>
    </div>
</div>

<!-- hide out of stock items -->
<div class="iwptp-option-row">
    <div class="iwptp-option-label"><?php esc_html_e("Hide out of stock items", 'ithemeland-woocommerce-product-table-pro-lite'); ?>:</div>
    <div class="iwptp-input">
        <label>
            <?php
            $disabled = get_option('woocommerce_hide_out_of_stock_items', 'no') == 'yes' ? 'disabled="disabled" checked="checked"' : '';
            ?>
            <input iwptp-model-key="hide_out_of_stock_items" type="checkbox" value="on" <?php echo esc_attr($disabled); ?> />
            <?php esc_html_e('Enable', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            <?php if ($disabled) : ?>
                <span class="iwptp-hide-out-of-stock-option-note">
                    <?php esc_html_e('To enable this option, please uncheck \'Out of stock visibility\'', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                    <a href="<?php echo esc_url(get_admin_url()); ?>admin.php?page=wc-settings&tab=products&section=inventory" target="_blank"> <?php esc_html_e('here', 'ithemeland-woocommerce-product-table-pro-lite'); ?></a>.
                </span>
            <?php endif; ?>
        </label>
    </div>
</div>

<!-- product type -->
<div class="iwptp-option-row">
    <div class="iwptp-option-label"><?php esc_html_e("Include Product types", 'ithemeland-woocommerce-product-table-pro-lite'); ?>:</div>
    <div class="iwptp-input">
        <select iwptp-model-key="include_product_types" class="iwptp-select2" data-placeholder="Select types ..." multiple>
            <?php
            if (!empty($product_types)) :
                foreach ($product_types as $type => $label) :
            ?>
                    <option value="<?php echo esc_attr($type); ?>"><?php echo esc_html($label); ?></option>
            <?php
                endforeach;
            endif;
            ?>
        </select>
    </div>
</div>

<div class="iwptp-option-row">
    <div class="iwptp-option-label"><?php esc_html_e("Exclude Product types", 'ithemeland-woocommerce-product-table-pro-lite'); ?>:</div>
    <div class="iwptp-input">
        <select iwptp-model-key="exclude_product_types" class="iwptp-select2" data-placeholder="Select types ..." multiple>
            <?php
            if (!empty($product_types)) :
                foreach ($product_types as $type => $label) :
            ?>
                    <option value="<?php echo esc_attr($type); ?>"><?php echo esc_html($label); ?></option>
            <?php
                endforeach;
            endif;
            ?>
        </select>
    </div>
</div>

<!-- User/Author/Vendor ID -->
<div class="iwptp-option-row">
    <div class="iwptp-option-label"><?php esc_html_e("User/Author/Vendor ID", 'ithemeland-woocommerce-product-table-pro-lite'); ?>:</div>
    <div class="iwptp-input">
        <select iwptp-model-key="include_users[]" class="iwptp-select2-users" iwptp-controller="include_users" data-placeholder="Select ..." multiple></select>
    </div>
</div>

<!-- include skus -->
<div class="iwptp-option-row">
    <div class="iwptp-option-label"><?php esc_html_e("Include SKUs", 'ithemeland-woocommerce-product-table-pro-lite'); ?>:</div>
    <div class="iwptp-input">
        <textarea iwptp-model-key="include_skus" placeholder="<?php esc_attr_e("Enter comma separated product SKUs", 'ithemeland-woocommerce-product-table-pro-lite'); ?>"></textarea>
        </select>
    </div>
</div>

<!-- exclude skus -->
<div class="iwptp-option-row">
    <div class="iwptp-option-label"><?php esc_html_e("Exclude SKUs", 'ithemeland-woocommerce-product-table-pro-lite'); ?>:</div>
    <div class="iwptp-input">
        <textarea iwptp-model-key="exclude_skus" placeholder="<?php esc_attr_e("Enter comma separated product SKUs", 'ithemeland-woocommerce-product-table-pro-lite'); ?>"></textarea>
        </select>
    </div>
</div>