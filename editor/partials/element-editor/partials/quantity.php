<div class="iwptp-element-settings-content-item active" data-content="general">
    <!-- display type -->
    <div class="iwptp-editor-row-option">
        <label>
            <?php esc_html_e('Display type', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
        <label><input type="radio" value="input" iwptp-model-key="display_type" /> <?php esc_html_e('Input box (numeric input field)', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <?php iwptp_radio('select', __('Dropdown (select field)', 'ithemeland-woocommerce-product-table-pro-lite'), 'display_type'); ?>
    </div>

    <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="display_type" iwptp-condition-val="select">
        <!-- max quantity -->
        <div class="iwptp-editor-row-option">
            <label>
                <?php esc_html_e('Max quantity (default)', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            </label>
            <input type="number" iwptp-model-key="max_qty" />
        </div>

        <!-- quantity label -->
        <div class="iwptp-editor-row-option">
            <label>
                <?php esc_html_e('Quantity label', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            </label>
            <input type="text" iwptp-model-key="qty_label" />
        </div>

    </div>

    <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="display_type" iwptp-condition-val="input">

        <!-- controls -->
        <div class="iwptp-editor-row-option">
            <label>
                <?php esc_html_e('Controls', 'ithemeland-woocommerce-product-table-pro-lite'); ?> +/-
            </label>
            <label><input type="radio" value="none" iwptp-model-key="controls" /> <?php esc_html_e('None', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <label><input type="radio" value="browser" iwptp-model-key="controls" /> <?php esc_html_e('Browser default', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <?php iwptp_radio('left_edge', __('Left edge', 'ithemeland-woocommerce-product-table-pro-lite'), 'controls'); ?>
            <?php iwptp_radio('right_edge', __('Right edge', 'ithemeland-woocommerce-product-table-pro-lite'), 'controls'); ?>
            <?php iwptp_radio('edges', __('Edges', 'ithemeland-woocommerce-product-table-pro-lite'), 'controls'); ?>
        </div>

        <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="controls" iwptp-condition-val="left_edge||right_edge||edges">
            <!-- initial value -->
            <div class="iwptp-editor-row-option">
                <label>
                    <?php esc_html_e('Initial value', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                </label>
                <label><input type="radio" value="min" iwptp-model-key="initial_value" /> <?php esc_html_e('Minimum quantity', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <?php iwptp_radio('0', '0', 'initial_value'); ?>
                <?php iwptp_radio('empty', __('Empty', 'ithemeland-woocommerce-product-table-pro-lite'), 'initial_value'); ?>
            </div>

            <!-- return to initial value after add to cart -->
            <div class="iwptp-editor-row-option">
                <label>
                    <input type="checkbox" iwptp-model-key="return_to_initial"> <?php esc_html_e('Return to initial value after add to cart (also clears checkbox)', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                </label>
            </div>

            <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="initial_value" iwptp-condition-val="min">
                <!-- return to minimum value when variation changes -->
                <div class="iwptp-editor-row-option">
                    <label>
                        <input type="checkbox" iwptp-model-key="reset_on_variation_change"> <?php esc_html_e('Reset to minimum quantity when variation is changed', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                    </label>
                </div>
            </div>

        </div>

        <!-- max warning -->
        <div class="iwptp-editor-row-option">
            <label>
                <?php esc_html_e('Warning when user enters value exceeding min quantity', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                <small><?php esc_html_e('Use placeholder', 'ithemeland-woocommerce-product-table-pro-lite'); ?>: [max]</small>
            </label>
            <input type="text" iwptp-model-key="qty_warning" />
        </div>

        <!-- min warning -->
        <div class="iwptp-editor-row-option">
            <label>
                <?php esc_html_e('Warning when user enters value below min quantity', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                <small><?php esc_html_e('Use placeholder', 'ithemeland-woocommerce-product-table-pro-lite'); ?>: [min]</small>
            </label>
            <input type="text" iwptp-model-key="min_qty_warning" />
        </div>

        <!-- step warning -->
        <div class="iwptp-editor-row-option">
            <label>
                <?php esc_html_e('Warning when user enters value not following step requirement', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                <small><?php esc_html_e('Use placeholder', 'ithemeland-woocommerce-product-table-pro-lite'); ?>: [step]</small>
            </label>
            <input type="text" iwptp-model-key="qty_step_warning" />
        </div>

    </div>

    <!-- hide if 1 limit order -->
    <div class="iwptp-editor-row-option">
        <?php iwptp_checkbox('true', 'Hide if only 1 allowed per order', 'hide_if_sold_individually'); ?>
    </div>

    <?php include('html-class.php'); ?>

    <?php include('condition/outer.php'); ?>
</div>

<div class="iwptp-element-settings-content-item" data-content="style">
    <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="display_type" iwptp-condition-val="input">
        <div iwptp-model-key="style">
            <div class="iwptp-editor-row-option iwptp-toggle-options iwptp-row-accordion" iwptp-model-key="[id].iwptp-display-type-input">

                <span class="iwptp-toggle-label">
                    <?php esc_html_e('Style for Element', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                    <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?>
                </span>

                <!-- font-size -->
                <div class="iwptp-editor-row-option">
                    <label><?php esc_html_e('Font size', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                    <input type="text" iwptp-model-key="font-size" placeholder="16px">
                </div>

                <!-- font-color -->
                <div class="iwptp-editor-row-option">
                    <label><?php esc_html_e('Font color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                    <input type="text" iwptp-model-key="color" placeholder="#000" class="iwptp-color-picker">
                </div>

                <!-- background-color -->
                <div class="iwptp-editor-row-option">
                    <label><?php esc_html_e('Background color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                    <input type="text" iwptp-model-key="background-color" class="iwptp-color-picker">
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

                <!-- width -->
                <div class="iwptp-editor-row-option">
                    <label><?php esc_html_e('Width', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                    <input type="text" iwptp-model-key="width" />
                </div>

                <!-- height -->
                <div class="iwptp-editor-row-option">
                    <label><?php esc_html_e('Height', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                    <input type="text" iwptp-model-key="height" />
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
        </div>
    </div>
    <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="display_type" iwptp-condition-val="select">
        <div iwptp-model-key="style">
            <div class="iwptp-editor-row-option iwptp-toggle-options iwptp-row-accordion" iwptp-model-key="[id] > .iwptp-qty-select">

                <span class="iwptp-toggle-label">
                    <?php esc_html_e('Style for Select element', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                    <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?>
                </span>

                <!-- font-size -->
                <div class="iwptp-editor-row-option">
                    <label><?php esc_html_e('Font size', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                    <input type="text" iwptp-model-key="font-size" placeholder="16px">
                </div>

                <!-- width -->
                <div class="iwptp-editor-row-option">
                    <label><?php esc_html_e('Width', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                    <input type="text" iwptp-model-key="width" />
                </div>

                <!-- height -->
                <div class="iwptp-editor-row-option">
                    <label><?php esc_html_e('Height', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                    <input type="text" iwptp-model-key="height" />
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
        </div>
    </div>
</div>