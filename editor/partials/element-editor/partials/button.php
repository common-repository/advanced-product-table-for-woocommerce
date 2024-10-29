<div class="iwptp-element-settings-content-item active" data-content="general">

    <!-- use default template -->
    <div class="iwptp-editor-row-option">
        <label>
            <input type="checkbox" iwptp-model-key="use_default_template">
            <?php esc_html_e('Use default WooCommerce button from product page', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
    </div>

    <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="use_default_template" iwptp-condition-val="false">
        <!-- label -->
        <div class="iwptp-editor-row-option">
            <label><?php esc_html_e('Label', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <div iwptp-block-editor iwptp-model-key="label" iwptp-be-add-row="0" iwptp-be-add-element-partial="add-button-element"></div>
        </div>

        <!-- link -->
        <div class="iwptp-editor-row-option">
            <label><?php esc_html_e('Link', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <?php
            $options = array(
                'product_link' => esc_html__(' Open product page', 'ithemeland-woocommerce-product-table-pro-lite'),
                'cart_ajax' => esc_html__(' Add to cart via AJAX', 'ithemeland-woocommerce-product-table-pro-lite'),
                'cart_refresh' => esc_html__(' Add to cart and refresh page', 'ithemeland-woocommerce-product-table-pro-lite'),
                'cart_redirect' => esc_html__(' Add to cart and redirect to cart page', 'ithemeland-woocommerce-product-table-pro-lite'),
                'cart_checkout' => esc_html__(' Add to cart and redirect to checkout page', 'ithemeland-woocommerce-product-table-pro-lite'),
                'external_link' => esc_html__(' Open external/affiliate link', 'ithemeland-woocommerce-product-table-pro-lite'),
            );

            foreach ($options as $val => $label) {
                echo '<label><input type="radio iwptp-model-key="link" value="' . esc_attr($val) . '" >' . esc_html($label) . '</label>';
            }
            ?>
            <?php iwptp_radio('custom_field', esc_html__('Custom field containing URL', 'ithemeland-woocommerce-product-table-pro-lite'), 'link'); ?>
            <?php iwptp_radio('custom_field_media_id', esc_html__('Custom field containing Media ID', 'ithemeland-woocommerce-product-table-pro-lite'), 'link'); ?>
            <?php iwptp_radio('custom_field_acf', esc_html__('Custom field managed by ACF', 'ithemeland-woocommerce-product-table-pro-lite'), 'link'); ?>
            <?php iwptp_radio('custom', esc_html__('Build custom URL with placeholders', 'ithemeland-woocommerce-product-table-pro-lite'), 'link'); ?>
            <?php iwptp_radio('cart_custom', esc_html__('Add to cart and redirect to custom URL', 'ithemeland-woocommerce-product-table-pro-lite'), 'link'); ?>
        </div>

        <!-- build custom url -->
        <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="link" iwptp-condition-val="custom||cart_custom" style="margin-top: 0;">
            <input iwptp-model-key="custom_url" type="text">
            <label>
                <?php iwptp_general_placeholders__print_placeholders(); ?>
            </label>
        </div>

        <!-- custom field -->
        <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="link" iwptp-condition-val="custom_field||custom_field_media_id||custom_field_acf">
            <label><?php esc_html_e('Custom field name / meta key', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <input iwptp-model-key="custom_field" type="text">
        </div>

        <!-- target -->
        <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="link" iwptp-condition-val="product_link||external_link||custom_field||custom_field_media_id||custom_field_acf||custom">
            <label><?php esc_html_e('Action on click', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <label>
                <input type="radio" iwptp-model-key="target" value="_self">
                <?php esc_html_e('Open the link on the same page', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            </label>
            <label>
                <input type="radio" iwptp-model-key="target" value="_blank">
                <?php esc_html_e('Open the link on a new page', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            </label>

            <div iwptp-panel-condition="prop" iwptp-condition-prop="link" iwptp-condition-val="custom_field||custom_field_media_id||custom_field_acf||custom">
                <label>
                    <input type="radio" iwptp-model-key="target" value="download">
                    <?php esc_html_e('Download the file', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                </label>
            </div>

        </div>

        <!-- empty value relabel -->
        <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="link" iwptp-condition-val="custom_field||custom_field_media_id">
            <label><?php esc_html_e('Output when no custom field value exists', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <div iwptp-model-key="custom_field_empty_relabel" iwptp-block-editor iwptp-be-add-row="0"></div>
        </div>

    </div>

    <?php include('html-class.php'); ?>

    <?php include('condition/outer.php'); ?>
</div>

<div class="iwptp-element-settings-content-item" data-content="style">
    <div class="iwptp-editor-row-option" iwptp-model-key="style">
        <div class="iwptp-editor-row-option iwptp-toggle-options iwptp-row-accordion" iwptp-model-key="[id]">
            <span class="iwptp-toggle-label">
                <?php esc_html_e('Style for Button', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?>
            </span>

            <!-- font-size -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Font size', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="font-size" />
            </div>

            <!-- font color -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Font color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="color" placeholder="#000" class="iwptp-color-picker">
            </div>

            <!-- font color on hover -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Font color on hover', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="color:hover" placeholder="#000" class="iwptp-color-picker">
            </div>

            <!-- font-weight -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Font weight', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <select iwptp-model-key="font-weight">
                    <option value="normal"><?php esc_html_e('Normal', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                    <option value="bold"><?php esc_html_e('Bold', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                    <option value="200"><?php esc_html_e('Light', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                </select>
            </div>

            <!-- letter-spacing -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Letter spacing', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="letter-spacing" placeholder="0px">
            </div>

            <!-- background color -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Background color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="background-color" class="iwptp-color-picker">
            </div>

            <!-- background color on hover -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Background color on hover', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="background-color:hover" class="iwptp-color-picker">
            </div>

            <!-- border -->
            <div class="iwptp-editor-row-option iwptp-borders-style">
                <label><?php esc_html_e('Border', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="border-width" placeholder="<?php esc_attr_e('width', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
                <select iwptp-model-key="border-style">
                    <option value="none"><?php esc_html_e('None', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                    <option value="solid"><?php esc_html_e('Solid', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                    <option value="dashed"><?php esc_html_e('Dashed', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                    <option value="dotted"><?php esc_html_e('Dotted', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                </select>
                <input type="text" iwptp-model-key="border-color" class="iwptp-color-picker" placeholder="<?php esc_attr_e('color', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
            </div>

            <!-- border-color on hover -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Border color on hover', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="border-color:hover" class="iwptp-color-picker" placeholder="<?php esc_attr_e('color', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
            </div>

            <!-- border-radius -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Border radius', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="border-radius">
            </div>

            <!-- width -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Force width', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="width" />
            </div>

            <!-- padding -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Padding', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="padding-top" placeholder="<?php esc_attr_e('top', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
                <input type="text" iwptp-model-key="padding-right" placeholder="<?php esc_attr_e('right', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
                <input type="text" iwptp-model-key="padding-bottom" placeholder="<?php esc_attr_e('bottom', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
                <input type="text" iwptp-model-key="padding-left" placeholder="<?php esc_attr_e('left', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
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

        <div class="iwptp-editor-row-option iwptp-toggle-options iwptp-row-accordion" iwptp-model-key="[id].iwptp-disabled">

            <span class="iwptp-toggle-label">
                <?php esc_html_e('Style when out of stock', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?>
            </span>

            <!-- opacity -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Opacity', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <?php
                $arr = array(
                    '.1' => '.1',
                    '.2' => '.2',
                    '.3' => '.3',
                    '.4' => '.4',
                    '.5' => '.5',
                    '.6' => '.6',
                    '.7' => '.7',
                    '.8' => '.8',
                    '.9' => '.9',
                    '1' => '1',
                );

                foreach ($arr as $key => $val) {
                ?>
                    <label class="iwptp-radio-wrapper">
                        <input type="radio" name="iwptp-opacity" iwptp-model-key="opacity" value="<?php echo esc_attr($val); ?>" />
                        <?php echo esc_html($key); ?>
                    </label>
                <?php
                }
                ?>
            </div>

            <!-- font color -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Font color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="color" placeholder="#000" class="iwptp-color-picker">
            </div>

            <!-- background color -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Background color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="background-color" class="iwptp-color-picker">
            </div>

            <!-- border-color -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Border color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="border-color" class="iwptp-color-picker" placeholder="<?php esc_attr_e('color', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
            </div>

        </div>

        <div class="iwptp-editor-row-option iwptp-toggle-options iwptp-row-accordion" iwptp-model-key="[id] .iwptp-cart-badge-number">

            <span class="iwptp-toggle-label">
                <?php esc_html_e('Style for Cart Badge', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?>
            </span>

            <!-- visibility -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Visibility', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <select iwptp-model-key="visibility">
                    <option value=""></option>
                    <option value="hidden"><?php esc_html_e('Hide', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                    <option value="visible"><?php esc_html_e('Show', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                </select>
            </div>

            <!-- font-size -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Font size', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="font-size" />
            </div>

            <!-- font color -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Font color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="color" placeholder="#000" class="iwptp-color-picker">
            </div>

            <!-- background color -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Background color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="background-color" class="iwptp-color-picker">
            </div>

            <!-- border -->
            <div class="iwptp-editor-row-option iwptp-borders-style">
                <label><?php esc_html_e('Border', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="border-width" placeholder="width">
                <select iwptp-model-key="border-style">
                    <option value="none"><?php esc_html_e('None', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                    <option value="solid"><?php esc_html_e('Solid', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                    <option value="dashed"><?php esc_html_e('Dashed', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                    <option value="dotted"><?php esc_html_e('Dotted', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                </select>
                <input type="text" iwptp-model-key="border-color" class="iwptp-color-picker" placeholder="<?php esc_attr_e('color', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
            </div>

            <!-- padding -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Padding', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="padding-top" placeholder="<?php esc_attr_e('top', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
                <input type="text" iwptp-model-key="padding-right" placeholder="<?php esc_attr_e('right', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
                <input type="text" iwptp-model-key="padding-bottom" placeholder="<?php esc_attr_e('bottom', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
                <input type="text" iwptp-model-key="padding-left" placeholder="<?php esc_attr_e('left', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
            </div>
        </div>
    </div>
</div>