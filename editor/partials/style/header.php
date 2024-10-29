<div iwptp-model-key="[container] .iwptp-header.iwptp-navigation">

    <!-- margin-bottom -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Gap from table', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="margin-bottom" style="width: 100% !important;" />
    </div>

    <!-- row_gap -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Gap between rows', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="row_gap" />
    </div>

    <!-- font-size -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Font size', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="font-size" />
    </div>

    <!-- line-height -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Line height', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="line-height" placeholder="1.2em">
    </div>

    <!-- font color -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Font color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="color" placeholder="#000" class="iwptp-color-picker">
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

    <!-- background color -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Background color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="background-color" class="iwptp-color-picker">
    </div>

    <!-- border -->
    <div class="iwptp-editor-option-row iwptp-borders-style">
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
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Border radius', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="border-radius">
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

<!-- Dropdown Heading -->
<div iwptp-model-key="[container] .iwptp-header.iwptp-navigation .iwptp-dropdown.iwptp-filter > .iwptp-filter-heading">

    <div class="iwptp-editor-option-row iwptp-separation-heading">
        <?php esc_html_e('Dropdown heading', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
    </div>

    <!-- font-size -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Font size', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="font-size" />
    </div>

    <!-- font-weight -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Font weight', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <select iwptp-model-key="font-weight">
            <option value="normal"><?php esc_html_e('Normal', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="bold"><?php esc_html_e('Bold', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="200"><?php esc_html_e('Light', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
        </select>
    </div>

    <!-- font color -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Font color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="color" placeholder="#000" class="iwptp-color-picker">
    </div>

    <!-- font color on hover -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Font color: hover', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="color:hover" placeholder="#000" class="iwptp-color-picker">
    </div>

    <!-- font color when active -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Font color: active', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="color:active" placeholder="#000" class="iwptp-color-picker">
    </div>

    <!-- background color -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Background color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="background-color" class="iwptp-color-picker">
    </div>

    <!-- background color on hover -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Background color: hover', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="background-color:hover" class="iwptp-color-picker">
    </div>

    <!-- background color when active -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Background color: active', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="background-color:active" class="iwptp-color-picker">
    </div>

    <!-- border color -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Border color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="border-color" class="iwptp-color-picker">
    </div>

    <!-- border color on hover -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Border color: hover', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="border-color:hover" class="iwptp-color-picker">
    </div>

    <!-- border color when active -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Border color: active', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="border-color:active" class="iwptp-color-picker">
    </div>

    <!-- border radius -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Border radius', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="border-radius" />
    </div>

    <!-- padding -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Padding', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="padding-top" placeholder="<?php esc_attr_e('top', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
        <input type="text" iwptp-model-key="padding-right" placeholder="<?php esc_attr_e('right', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
        <input type="text" iwptp-model-key="padding-bottom" placeholder="<?php esc_attr_e('bottom', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
        <input type="text" iwptp-model-key="padding-left" placeholder="<?php esc_attr_e('left', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
    </div>

    <!-- margin -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Margin', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="margin-top" placeholder="<?php esc_attr_e('top', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
        <input type="text" iwptp-model-key="margin-right" placeholder="<?php esc_attr_e('right', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
        <input type="text" iwptp-model-key="margin-bottom" placeholder="<?php esc_attr_e('bottom', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
        <input type="text" iwptp-model-key="margin-left" placeholder="<?php esc_attr_e('left', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
    </div>

</div>

<!-- Dropdown Menu -->
<div iwptp-model-key="[container] .iwptp-header.iwptp-navigation .iwptp-dropdown.iwptp-filter > .iwptp-dropdown-menu">

    <div class="iwptp-editor-option-row iwptp-separation-heading">
        <?php esc_html_e('Dropdown menu', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
    </div>

    <!-- font-size -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Font size', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="font-size" />
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

    <!-- font color -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Font color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="color" placeholder="#000" class="iwptp-color-picker">
    </div>

    <!-- background color -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Background color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="background-color" class="iwptp-color-picker">
    </div>

    <!-- border color -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Border color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="border-color" class="iwptp-color-picker">
    </div>

    <!-- border radius -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Border radius', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="border-radius" />
    </div>

    <!-- width -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Width', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="width" />
    </div>

    <!-- max-height -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Max height', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="max-height" />
    </div>

    <!-- padding -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Padding', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="padding-top" placeholder="<?php esc_attr_e('top', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
        <input type="text" iwptp-model-key="padding-right" placeholder="<?php esc_attr_e('right', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
        <input type="text" iwptp-model-key="padding-bottom" placeholder="<?php esc_attr_e('bottom', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
        <input type="text" iwptp-model-key="padding-left" placeholder="<?php esc_attr_e('left', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
    </div>

    <!-- margin -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Margin', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="margin-top" placeholder="<?php esc_attr_e('top', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
        <input type="text" iwptp-model-key="margin-right" placeholder="<?php esc_attr_e('right', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
        <input type="text" iwptp-model-key="margin-bottom" placeholder="<?php esc_attr_e('bottom', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
        <input type="text" iwptp-model-key="margin-left" placeholder="<?php esc_attr_e('left', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
    </div>

</div>

<!-- Dropdown Search -->
<div iwptp-model-key="[container] .iwptp-header.iwptp-navigation .iwptp-dropdown.iwptp-filter > .iwptp-dropdown-menu .iwptp-search-filter-options">

    <div class="iwptp-editor-option-row iwptp-separation-heading">
        <?php esc_html_e('Dropdown search', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
    </div>

    <!-- font-size -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Font size', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="font-size" />
    </div>

    <!-- background color -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Background color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="background-color" class="iwptp-color-picker">
    </div>

    <!-- height -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Height', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="height" />
    </div>

    <!-- border -->
    <div class="iwptp-editor-option-row iwptp-borders-style">
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

    <!-- border radius -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Border radius', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="border-radius" />
    </div>

    <!-- padding -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Padding', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="padding-top" placeholder="<?php esc_attr_e('top', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
        <input type="text" iwptp-model-key="padding-right" placeholder="<?php esc_attr_e('right', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
        <input type="text" iwptp-model-key="padding-bottom" placeholder="<?php esc_attr_e('bottom', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
        <input type="text" iwptp-model-key="padding-left" placeholder="<?php esc_attr_e('left', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
    </div>

    <!-- margin -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Margin', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="margin-top" placeholder="<?php esc_attr_e('top', 'ithemeland-woocommerce-product-table-pro-lite'); ?>" class="iwptp-width--50-percent">
        <input type="text" iwptp-model-key="margin-bottom" placeholder="<?php esc_attr_e('bottom', 'ithemeland-woocommerce-product-table-pro-lite'); ?>" class="iwptp-width--50-percent">
    </div>

</div>

<!-- Row heading -->
<div iwptp-model-key="[container] .iwptp-header.iwptp-navigation .iwptp-options-row.iwptp-filter > .iwptp-filter-heading">

    <div class="iwptp-editor-option-row iwptp-separation-heading">
        <?php esc_html_e('Row heading', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
    </div>

    <!-- font-size -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Font size', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="font-size" />
    </div>

    <!-- font-weight -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Font weight', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <select iwptp-model-key="font-weight">
            <option value="normal"><?php esc_html_e('Normal', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="bold"><?php esc_html_e('Bold', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="200"><?php esc_html_e('Light', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
        </select>
    </div>

    <!-- font color -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Font color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="color" placeholder="#000" class="iwptp-color-picker">
    </div>

    <!-- padding -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Padding', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="padding-top" placeholder="<?php esc_attr_e('top', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
        <input type="text" iwptp-model-key="padding-right" placeholder="<?php esc_attr_e('right', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
        <input type="text" iwptp-model-key="padding-bottom" placeholder="<?php esc_attr_e('bottom', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
        <input type="text" iwptp-model-key="padding-left" placeholder="<?php esc_attr_e('left', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
    </div>

    <!-- margin -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Margin', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="margin-top" placeholder="<?php esc_attr_e('top', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
        <input type="text" iwptp-model-key="margin-right" placeholder="<?php esc_attr_e('right', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
        <input type="text" iwptp-model-key="margin-bottom" placeholder="<?php esc_attr_e('bottom', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
        <input type="text" iwptp-model-key="margin-left" placeholder="<?php esc_attr_e('left', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
    </div>

</div>

<!-- Row options -->
<div iwptp-model-key="[container] .iwptp-header.iwptp-navigation .iwptp-options-row.iwptp-filter > .iwptp-options > .iwptp-option">

    <div class="iwptp-editor-option-row iwptp-separation-heading">
        <?php esc_html_e('Row options', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
    </div>

    <!-- font-size -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Font size', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="font-size" />
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

    <!-- font color -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Font color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="color" placeholder="#000" class="iwptp-color-picker">
    </div>

    <!-- font color:hover -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Font color: hover', 'ithemeland-woocommerce-product-table-pro-lite'); ?> </label>
        <input type="text" iwptp-model-key="color:hover" placeholder="#000" class="iwptp-color-picker">
    </div>

    <!-- font color:selected -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Font color: selected', 'ithemeland-woocommerce-product-table-pro-lite'); ?> </label>
        <input type="text" iwptp-model-key="color:selected" placeholder="#000" class="iwptp-color-picker">
    </div>

    <!-- background color -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Background color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="background-color" class="iwptp-color-picker">
    </div>

    <!-- background color:hover -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Bg color: hover', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="background-color:hover" class="iwptp-color-picker">
    </div>

    <!-- background color:selected -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Bg color: selected', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="background-color:selected" class="iwptp-color-picker">
    </div>

    <!-- border color -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Border: color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="border-color" class="iwptp-color-picker">
    </div>

    <!-- border color:hover -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Border color: hover', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="border-color:hover" class="iwptp-color-picker">
    </div>

    <!-- border color:selected -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Border color: selected', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="border-color:selected" class="iwptp-color-picker">
    </div>

    <!-- border radius -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Border radius', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="border-radius" />
    </div>

    <!-- width -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Width', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="width" />
    </div>

    <!-- padding -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Padding', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="padding-top" placeholder="<?php esc_attr_e('top', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
        <input type="text" iwptp-model-key="padding-right" placeholder="<?php esc_attr_e('right', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
        <input type="text" iwptp-model-key="padding-bottom" placeholder="<?php esc_attr_e('bottom', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
        <input type="text" iwptp-model-key="padding-left" placeholder="<?php esc_attr_e('left', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
    </div>

    <!-- margin -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Margin', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="margin-top" placeholder="<?php esc_attr_e('top', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
        <input type="text" iwptp-model-key="margin-right" placeholder="<?php esc_attr_e('right', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
        <input type="text" iwptp-model-key="margin-bottom" placeholder="<?php esc_attr_e('bottom', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
        <input type="text" iwptp-model-key="margin-left" placeholder="<?php esc_attr_e('left', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
    </div>

</div>