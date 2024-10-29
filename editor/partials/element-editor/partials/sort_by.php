<div class="iwptp-element-settings-content-item active" data-content="general">
    <!-- display type -->
    <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="position" iwptp-condition-val="header">
        <label><?php esc_html_e('Display type', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <select iwptp-model-key="display_type">
            <option value="dropdown"><?php esc_html_e('Dropdown', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="row"><?php esc_html_e('Row', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
        </select>
    </div>

    <!-- heading -->
    <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="display_type" iwptp-condition-val="row">
        <label><?php esc_html_e('Heading', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="heading">
    </div>

    <div class="iwptp-editor-row-option">
        <label class="iwptp-editor-options-heading"><?php esc_html_e('Sort Options', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
    </div>

    <!-- options -->
    <div class="iwptp-editor-row-option">

        <!-- option rows -->
        <div class="iwptp-label-options-rows-wrapper iwptp-sortable" iwptp-model-key="dropdown_options">
            <div class="iwptp-editor-row iwptp-editor-custom-label-setup" iwptp-controller="custom_labels" iwptp-model-key="[]" iwptp-model-key-index="0" iwptp-initial-data="sortby_option" iwptp-row-template="sortby_option">

                <!-- label -->
                <div class="iwptp-editor-row-option">
                    <label><?php esc_html_e('Label', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                    <input type="text" iwptp-model-key="label">
                </div>

                <!-- Orderby -->
                <div class="iwptp-editor-row-option">
                    <label><?php esc_html_e('Order by', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                    <select iwptp-model-key="orderby">
                        <option value="title"><?php esc_html_e('Title', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                        <option value="price"><?php esc_html_e('Price low to high', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                        <option value="price-desc"><?php esc_html_e('Price high to low', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                        <option value="menu_order"><?php esc_html_e('Menu order', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                        <option value="popularity"><?php esc_html_e('Popularity (sales)', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                        <option value="rating"><?php esc_html_e('Average rating', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                        <option value="rand"><?php esc_html_e('Random', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                        <option value="date"><?php esc_html_e('Date', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>

                        <?php iwptp_option('category', __('Category', 'ithemeland-woocommerce-product-table-pro-lite')); ?>
                        <?php iwptp_option('attribute', __('Attribute: as text', 'ithemeland-woocommerce-product-table-pro-lite')); ?>
                        <?php iwptp_option('attribute_num', __('Attribute: as number', 'ithemeland-woocommerce-product-table-pro-lite')); ?>
                        <?php iwptp_option('taxonomy', __('Taxonomy', 'ithemeland-woocommerce-product-table-pro-lite')); ?>
                        <?php iwptp_option('meta_value_num', __('Custom field: as number', 'ithemeland-woocommerce-product-table-pro-lite')); ?>
                        <?php iwptp_option('meta_value', __('Custom field: as text', 'ithemeland-woocommerce-product-table-pro-lite')); ?>
                        <?php iwptp_option('id', __('Product ID', 'ithemeland-woocommerce-product-table-pro-lite')); ?>
                        <?php iwptp_option('sku', __('SKU: as text', 'ithemeland-woocommerce-product-table-pro-lite')); ?>
                        <?php iwptp_option('sku_num', __('SKU: as integer', 'ithemeland-woocommerce-product-table-pro-lite')); ?>
                    </select>
                </div>

                <!-- meta key -->
                <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="orderby" iwptp-condition-val="meta_value_num||meta_value">
                    <label><?php esc_html_e('Custom field name', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
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

                <!-- order -->
                <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="orderby" iwptp-condition-val="meta_value_num||meta_value||title||menu_order||id||sku||sku_num||date||category||attribute||attribute_num||taxonomy">
                    <label><?php esc_html_e('Order', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                    <select iwptp-model-key="order">
                        <option value="ASC"><?php esc_html_e('Low to high', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                        <option value="DESC"><?php esc_html_e('High to low', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                    </select>
                </div>

                <!-- corner options -->
                <?php iwptp_corner_options(); ?>

            </div>

            <button class="iwptp-button" iwptp-add-row-template="sortby_option">
                <?php esc_html_e('Add an Option', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            </button>

        </div>
    </div>

    <!-- accordion always open -->
    <div class="iwptp-editor-row-option">
        <label>
            <input type="checkbox" iwptp-model-key="accordion_always_open"> <?php esc_html_e('Keep filter open by default if it is in sidebar', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
    </div>

</div>
<div class="iwptp-element-settings-content-item" data-content="style">
    <?php include('style/filter.php'); ?>
</div>