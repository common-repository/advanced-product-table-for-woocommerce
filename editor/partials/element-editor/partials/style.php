<div class="iwptp-editor-row-style-options iwptp-toggle-options" iwptp-model-key="style">

    <div class="iwptp-editor-row-option iwptp-toggle-label">
        <?php esc_html_e('Style Options', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        <?php echo wp_kses(iwptp_icon('chevron-down', 'iwptp-toggle-icon'), iwptp_allowed_html_tags()); ?>
    </div>

    <div class="iwptp-wrapper" iwptp-model-key="[id]">

        <!-- font-size -->
        <div class="iwptp-editor-row-option">
            <label><?php esc_html_e('Font size', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <input type="text" iwptp-model-key="font-size" placeholder="16px">
        </div>

        <!-- line-height -->
        <div class="iwptp-editor-row-option">
            <label><?php esc_html_e('Line height', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <input type="text" iwptp-model-key="line-height" placeholder="1.2em">
        </div>

        <!-- font-weight -->
        <div class="iwptp-editor-row-option">
            <label><?php esc_html_e('Font weight', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <select iwptp-model-key="font-weight">
                <option value="normal"><?php esc_html_e('Normal', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                <option value="bold"><?php esc_html_e('Bold', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                <option value="lighter"><?php esc_html_e('Light', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            </select>
        </div>

        <!-- font-color -->
        <div class="iwptp-editor-row-option">
            <label><?php esc_html_e('Font color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <input type="text" iwptp-model-key="color" placeholder="#000" class="iwptp-color-picker">
        </div>

        <!-- text-transform -->
        <div class="iwptp-editor-row-option">
            <label><?php esc_html_e('Text transform', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <select iwptp-model-key="text-transform">
                <option value="none"><?php esc_html_e('None', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                <option value="uppercase"><?php esc_html_e('Upper case', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                <option value="capitalize"><?php esc_html_e('Capitalize', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                <option value="lowercase"><?php esc_html_e('Lower case', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            </select>
        </div>

        <!-- letter-spacing -->
        <div class="iwptp-editor-row-option">
            <label><?php esc_html_e('Letter spacing', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <input type="text" iwptp-model-key="letter-spacing" placeholder="0px">
        </div>

        <!-- background-color -->
        <div class="iwptp-editor-row-option">
            <label><?php esc_html_e('Background color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <input type="text" iwptp-model-key="background-color" class="iwptp-color-picker">
        </div>

        <!-- border -->
        <div class="iwptp-editor-row-option">
            <label><?php esc_html_e('Border', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <input type="text" iwptp-model-key="border-width" placeholder="width">
            <select iwptp-model-key="border-style">
                <option value="none"><?php esc_html_e('None', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                <option value="solid"><?php esc_html_e('Solid', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                <option value="dashed"><?php esc_html_e('Dashed', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                <option value="dotted"><?php esc_html_e('Dotted', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            </select>
            <input type="text" iwptp-model-key="border-color" class="iwptp-color-picker" placeholder="color">
        </div>

        <!-- border-radius -->
        <div class="iwptp-editor-row-option">
            <label><?php esc_html_e('Border radius', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <input type="text" iwptp-model-key="border-radius">
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

        <!-- padding -->
        <div class="iwptp-editor-row-option">
            <label><?php esc_html_e('Padding', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <input type="text" iwptp-model-key="padding-top" placeholder="<?php esc_attr_e('top', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
            <input type="text" iwptp-model-key="padding-right" placeholder="<?php esc_attr_e('right', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
            <input type="text" iwptp-model-key="padding-bottom" placeholder="<?php esc_attr_e('bottom', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
            <input type="text" iwptp-model-key="padding-left" placeholder="<?php esc_attr_e('left', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
        </div>

        <!-- display -->
        <div class="iwptp-editor-row-option">
            <label><?php esc_html_e('Display', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <select iwptp-model-key="display">
                <option value="inline-block"><?php esc_html_e('Inline-block', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                <option value="inline"><?php esc_html_e('Inline', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                <option value="block"><?php esc_html_e('Block', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            </select>
        </div>
    </div>
</div>