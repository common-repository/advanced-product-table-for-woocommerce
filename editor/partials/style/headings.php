<!-- hide -->
<div class="iwptp-editor-option-row" iwptp-model-key="[container] .iwptp-table .iwptp-heading-row">
    <label><?php esc_html_e('Toggle', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
    <select iwptp-model-key="display">
        <option value="table-row"><?php esc_html_e('Show', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
        <option value="none"><?php esc_html_e('Hide', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
    </select>
</div>

<div class="" iwptp-model-key="[container] thead .iwptp-heading">

    <!-- text-align -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Text align', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <select iwptp-model-key="text-align">
            <option value="center"><?php esc_html_e('Center', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="left"><?php esc_html_e('Left', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="right"><?php esc_html_e('Right', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="justify"><?php esc_html_e('Justify', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
        </select>
    </div>

    <!-- vertical-align -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Vertical align', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <select iwptp-model-key="vertical-align">
            <option value="middle"><?php esc_html_e('Middle', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="top"><?php esc_html_e('Top', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="baseline"><?php esc_html_e('Baseline', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="bottom"><?php esc_html_e('Bottom', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
        </select>
    </div>

    <!-- font-size -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Font size', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="font-size" placeholder="16px">
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

    <!-- font-weight -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Font weight', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <select iwptp-model-key="font-weight">
            <option value=""></option>
            <option value="normal"><?php esc_html_e('Normal', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="bold"><?php esc_html_e('Bold', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="light"><?php esc_html_e('Light', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="100">100</option>
            <option value="200">200</option>
            <option value="300">300</option>
            <option value="400">400</option>
            <option value="500">500</option>
            <option value="600">600</option>
            <option value="700">700</option>
            <option value="800">800</option>
            <option value="900">900</option>
        </select>
    </div>

    <!-- font-color -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Font color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="color" placeholder="#000" class="iwptp-color-picker">
    </div>

    <!-- text-transform -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Text transform', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <select iwptp-model-key="text-transform">
            <option value="none"><?php esc_html_e('None', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="uppercase"><?php esc_html_e('Upper case', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="capitalize"><?php esc_html_e('Capitalize', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="lowercase"><?php esc_html_e('Lower case', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
        </select>
    </div>

    <!-- letter-spacing -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Letter spacing', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="letter-spacing" placeholder="0px">
    </div>

    <!-- background-color -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Background color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="background-color" class="iwptp-color-picker">
    </div>

    <!-- padding -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Padding', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="padding-top" placeholder="<?php esc_attr_e('top', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
        <input type="text" iwptp-model-key="padding-right" placeholder="<?php esc_attr_e('right', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
        <input type="text" iwptp-model-key="padding-bottom" placeholder="<?php esc_attr_e('bottom', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
        <input type="text" iwptp-model-key="padding-left" placeholder="<?php esc_attr_e('left', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
    </div>

</div>