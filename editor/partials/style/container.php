<div iwptp-model-key="[container], [container] *">
    <!-- direction -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Direction', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <select iwptp-model-key="direction" placeholder="top">
            <option value="ltr"><?php esc_html_e('Left To Right', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="rtl"><?php esc_html_e('Right To Left', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
        </select>
    </div>

</div>

<div iwptp-model-key="[container],  [container] .iwptp-heading, [container] .iwptp-cell">
    <!-- background-color -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Background color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="background-color" class="iwptp-color-picker">
    </div>

    <!-- font-size -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Font size', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="font-size" placeholder="16px">
    </div>

    <!-- font-color -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Font color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="color" placeholder="#000" class="iwptp-color-picker">
    </div>

    <!-- font-family -->
    <div class="iwptp-editor-option-row">
        <label>
            <?php esc_html_e('Font family', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            <span class="iwptp-font-family-sample-text"><?php esc_html_e('Sample Text', 'ithemeland-woocommerce-product-table-pro-lite'); ?></span>
        </label>
        <select iwptp-model-key="font-family" class="iwptp-select2 iwptp-font-family-dropdown">
            <?php include IWPTPL_PLUGIN_PATH . "editor/partials/element-editor/partials/font-family/font-family-options.php"; ?>
        </select>
    </div>
</div>

<div iwptp-model-key="[container]">

    <!-- margin -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Margin', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="margin-top" placeholder="<?php esc_attr_e('top', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
        <input type="text" iwptp-model-key="margin-right" placeholder="<?php esc_attr_e('right', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
        <input type="text" iwptp-model-key="margin-bottom" placeholder="<?php esc_attr_e('bottom', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
        <input type="text" iwptp-model-key="margin-left" placeholder="<?php esc_attr_e('left', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
    </div>

    <!-- padding -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Padding', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="padding-top" placeholder="<?php esc_attr_e('top', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
        <input type="text" iwptp-model-key="padding-right" placeholder="<?php esc_attr_e('right', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
        <input type="text" iwptp-model-key="padding-bottom" placeholder="<?php esc_attr_e('bottom', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
        <input type="text" iwptp-model-key="padding-left" placeholder="<?php esc_attr_e('left', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
    </div>

    <!-- max-width -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Max-width', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="max-width">
    </div>

</div>