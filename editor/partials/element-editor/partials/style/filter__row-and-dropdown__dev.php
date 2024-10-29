<!-- Container -->
<div class="iwptp-editor-row-option iwptp-toggle-options iwptp-row-accordion" iwptp-model-key="[id]">
    <span class="iwptp-toggle-label">
        <?php esc_html_e('Style for Container', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?>
    </span>

    <!-- font-size -->
    <div class="iwptp-editor-row-option">
        <label><?php esc_html_e('Font size', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="font-size" />
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

    <!-- border color -->
    <div class="iwptp-editor-row-option">
        <label><?php esc_html_e('Border color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="border-color" class="iwptp-color-picker">
    </div>

    <!-- border color on hover -->
    <div class="iwptp-editor-row-option">
        <label><?php esc_html_e('Border color on hover', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="border-color:hover" class="iwptp-color-picker">
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

<!-- Menu -->
<div class="iwptp-editor-row-option iwptp-toggle-options iwptp-row-accordion" iwptp-model-key="[id] > .iwptp-dropdown-menu">
    <span class="iwptp-toggle-label">
        <?php esc_html_e('Style for Menu list', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?>
    </span>

    <!-- font-size -->
    <div class="iwptp-editor-row-option">
        <label><?php esc_html_e('Font size', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="font-size" />
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

    <!-- border color -->
    <div class="iwptp-editor-row-option">
        <label><?php esc_html_e('Border color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="border-color" class="iwptp-color-picker">
    </div>

    <!-- width -->
    <div class="iwptp-editor-row-option">
        <label><?php esc_html_e('Width', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="width" />
    </div>

    <!-- max-height -->
    <div class="iwptp-editor-row-option">
        <label><?php esc_html_e('Max height', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="max-height" />
    </div>

    <!-- padding -->
    <div class="iwptp-editor-row-option">
        <label><?php esc_html_e('Padding', 'ithemeland-woocommerce-product-table-pro-lite'); ?>Padding</label>
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