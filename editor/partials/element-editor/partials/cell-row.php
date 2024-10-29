<div class="iwptp-element-settings-content-item active" data-content="general">
    <div class="iwptp-editor-row-option iwptp-toggle-options iwptp-row-accordion">
        <span class="iwptp-toggle-label">
            <?php esc_html_e('Condition for Row', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?>
        </span>
        <?php require('condition/inner.php'); ?>
    </div>

    <?php include('html-class.php'); ?>
</div>

<!-- style -->
<div class="iwptp-element-settings-content-item" data-content="style">
    <div class="iwptp-editor-row-option" iwptp-model-key="style">
        <div class="iwptp-editor-row-option iwptp-toggle-options iwptp-row-accordion" iwptp-model-key="[id]">

            <span class="iwptp-toggle-label">
                <?php esc_html_e('Style for Row', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?>
            </span>

            <!-- margin-top -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Gap above', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="margin-top" class="iwptp-margin-input-force-full-width">
            </div>

            <!-- margin-bottom -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Gap below', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="margin-bottom" class="iwptp-margin-input-force-full-width">
            </div>

            <!-- padding -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Padding', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="padding-top" placeholder="<?php esc_attr_e('top', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
                <input type="text" iwptp-model-key="padding-right" placeholder="<?php esc_attr_e('right', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
                <input type="text" iwptp-model-key="padding-bottom" placeholder="<?php esc_attr_e('bottom', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
                <input type="text" iwptp-model-key="padding-left" placeholder="<?php esc_attr_e('left', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
            </div>

            <!-- text-align -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Text align', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <select iwptp-model-key="text-align">
                    <option value=""><?php esc_html_e('Auto', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                    <option value="center"><?php esc_html_e('Center', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                    <option value="left"><?php esc_html_e('Left', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                    <option value="right"><?php esc_html_e('Right', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                </select>
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

            <!-- border-radius -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Border radius', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="border-radius">
            </div>
        </div>
    </div>
</div>