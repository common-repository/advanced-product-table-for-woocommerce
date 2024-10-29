<div class="iwptp-element-settings-content-item active" data-content="general">

    <!-- sorting options -->
    <div class="iwptp-editor-row-option">
        <label><?php esc_html_e('Sort by', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <select class="" iwptp-model-key="orderby" iwptp-initial-data="title">
            <option value="title"><?php esc_html_e('Title', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="price"><?php esc_html_e('Price', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="menu_order"><?php esc_html_e('Menu order', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="popularity"><?php esc_html_e('Popularity (sales)', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="rating"><?php esc_html_e('Rating', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="date"><?php esc_html_e('Date', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>

            <?php iwptp_option('category', __('Category', 'ithemeland-woocommerce-product-table-pro-lite')); ?>
            <?php iwptp_option('attribute', __('Attribute: as text', 'ithemeland-woocommerce-product-table-pro-lite')); ?>
            <?php iwptp_option('attribute_num', __('Attribute: as number', 'ithemeland-woocommerce-product-table-pro-lite')); ?>
            <?php iwptp_option('taxonomy', __('Taxonomy', 'ithemeland-woocommerce-product-table-pro-lite')); ?>

            <option value="meta_value_num"><?php esc_html_e('Custom field: as number', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="meta_value"><?php esc_html_e('Custom field: as text', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="id"><?php esc_html_e('Product ID', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="sku"><?php esc_html_e('SKU: as text', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="sku_num"><?php esc_html_e('SKU: as integer', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
        </select>
    </div>

    <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="orderby" iwptp-condition-val="meta_value_num||meta_value">
        <label for=""><?php esc_html_e('Sort by custom field key', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="meta_key">
    </div>

    <!-- orderby: category -->
    <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="orderby" iwptp-condition-val="category">
        <div class="iwptp-editor-row-option">
            <label>
                <?php esc_html_e('Ignore categories', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                <small><?php esc_html_e('Optional. Enter one category slug per line', 'ithemeland-woocommerce-product-table-pro-lite'); ?></small>
            </label>
            <div class="iwptp-input">
                <textarea iwptp-model-key="orderby_ignore_category"></textarea>
            </div>
        </div>

        <div class="iwptp-editor-row-option">
            <label>
                <?php esc_html_e('Focus categories', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                <small><?php esc_html_e('Optional. Enter one category slug per line', 'ithemeland-woocommerce-product-table-pro-lite'); ?></small>
            </label>
            <div class="iwptp-input">
                <textarea iwptp-model-key="orderby_focus_category"></textarea>
            </div>
        </div>
    </div>

    <!-- orderby: attribute -->
    <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="orderby" iwptp-condition-val="attribute||attribute_num">
        <div class="iwptp-editor-row-option">
            <label><?php esc_html_e('Orderby attribute', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <div class="iwptp-input">
                <select iwptp-model-key="orderby_attribute">
                    <option value=""><?php esc_html_e('Select an attribute here', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                    <?php
                    foreach (wc_get_attribute_taxonomies() as $attribute) {
                    ?>
                        <option value="pa_<?php echo esc_attr($attribute->attribute_name); ?>">
                            <?php echo esc_html($attribute->attribute_label); ?>
                        </option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="iwptp-editor-row-option">
            <label>
                <?php esc_html_e('Ignore attribute terms', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                <br>
                <small><?php esc_html_e('Optional. Enter one attribute term slug per line', 'ithemeland-woocommerce-product-table-pro-lite'); ?></small>
            </label>
            <div class="iwptp-input">
                <textarea iwptp-model-key="orderby_ignore_attribute_term"></textarea>
            </div>
        </div>

        <div class="iwptp-editor-row-option">
            <label>
                <?php esc_html_e('Focus attribute terms', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                <br>
                <small><?php esc_html_e('Optional. Enter one attribute term slug per line', 'ithemeland-woocommerce-product-table-pro-lite'); ?></small>
            </label>
            <div class="iwptp-input">
                <textarea iwptp-model-key="orderby_focus_attribute_term"></textarea>
            </div>
        </div>

        <div class="iwptp-editor-row-option">
            <label>
                <input iwptp-model-key="orderby_attribute_include_all" type="checkbox" value="on" />
                <?php esc_html_e('Include all', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                <small><?php esc_html_e('Show products that don\'t have the attribute, after sorted products', 'ithemeland-woocommerce-product-table-pro-lite'); ?></small>
            </label>
        </div>

    </div>

    <!-- orderby: taxonomy -->
    <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="orderby" iwptp-condition-val="taxonomy">
        <div class="iwptp-editor-row-option">
            <label><?php esc_html_e('Orderby taxonomy', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <div class="iwptp-input">
                <select iwptp-model-key="orderby_taxonomy">
                    <option value=""><?php esc_html_e('Select a taxonomy here', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                    <?php
                    $taxonomies = get_taxonomies(
                        array(
                            'public' => true,
                            '_builtin' => false,
                            'object_type' => array('product'),
                        ),
                        'objects'
                    );

                    foreach ($taxonomies as $taxonomy) {
                        if (
                            in_array($taxonomy->name, array('product_cat', 'product_shipping_class')) ||
                            'pa_' == substr($taxonomy->name, 0, 3)
                        ) {
                            continue;
                        }
                    ?>
                        <option value="<?php echo esc_attr($taxonomy->name); ?>">
                            <?php echo esc_html($taxonomy->label); ?>
                        </option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="iwptp-editor-row-option">
            <label>
                <?php esc_html_e('Ignore taxonomy terms', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                <br>
                <small><?php esc_html_e('Optional. Enter one taxonomy term slug per line', 'ithemeland-woocommerce-product-table-pro-lite'); ?></small>
            </label>
            <div class="iwptp-input">
                <textarea iwptp-model-key="orderby_ignore_taxonomy_term"></textarea>
            </div>
        </div>

        <div class="iwptp-editor-row-option">
            <label>
                <?php esc_html_e('Focus taxonomy terms', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                <br>
                <small><?php esc_html_e('Optional. Enter one taxonomy term slug per line', 'ithemeland-woocommerce-product-table-pro-lite'); ?></small>
            </label>
            <div class="iwptp-input">
                <textarea iwptp-model-key="orderby_focus_taxonomy_term"></textarea>
            </div>
        </div>

        <div class="iwptp-editor-row-option">
            <label>
                <input iwptp-model-key="orderby_taxonomy_include_all" type="checkbox" value="on" />
                <?php esc_html_e('Include all', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                <small><?php esc_html_e('Show products that don\'t have the taxonomy, after sorted products', 'ithemeland-woocommerce-product-table-pro-lite'); ?></small>
            </label>
        </div>
    </div>

    <?php include('html-class.php'); ?>
</div>

<!-- style -->
<div class="iwptp-element-settings-content-item" data-content="style">
    <div class="iwptp-editor-row-option" iwptp-model-key="style">
        <div class="iwptp-wrapper iwptp-editor-row-option" iwptp-model-key="[id]">

            <!-- font-size -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Size', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="font-size" style="margin-bottom: 0 !important;">
            </div>

            <!-- margin -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Margin', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="margin-top" placeholder="<?php esc_attr_e('top', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
                <input type="text" iwptp-model-key="margin-right" placeholder="<?php esc_attr_e('right', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
                <input type="text" iwptp-model-key="margin-bottom" placeholder="<?php esc_attr_e('bottom', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
                <input type="text" iwptp-model-key="margin-left" placeholder="<?php esc_attr_e('left', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
            </div>

        </div>

        <div class="iwptp-wrapper iwptp-editor-row-option" iwptp-model-key="[id] > .iwptp-inactive">

            <!-- font-color -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Color - inactive', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="color" class="iwptp-color-picker">
            </div>

        </div>

        <div class="iwptp-wrapper iwptp-editor-row-option" iwptp-model-key="[id] > .iwptp-active">

            <!-- font-color -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Color - active', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="color" class="iwptp-color-picker">
            </div>

        </div>
    </div>
</div>