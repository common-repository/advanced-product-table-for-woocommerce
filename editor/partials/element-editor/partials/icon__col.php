<div class="iwptp-element-settings-content-item active" data-content="general">
    <div class="iwptp-editor-row-option">
        <label><?php esc_html_e('Icon name', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <select class="iwptp-select-icon" iwptp-model-key="name" style="width: 100%;">
            <?php
            $path = IWPTPL_PLUGIN_PATH . 'assets/feather';
            $icons = array_diff(scandir($path), array('..', '.', '.DS_Store'));
            foreach ($icons as $icon) {
                if ($icon) {
                    $icon_name = substr($icon, 0, -4);
                    echo '<option value="' . esc_attr($icon_name) . '">' . esc_html(str_replace('-', ' ', ucfirst($icon_name))) . '</option>';
                }
            }
            ?>
        </select>
    </div>

    <div class="iwptp-editor-row-option">
        <label>
            <?php esc_html_e('Title', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            <small><?php esc_html_e('Text that shows up on mouse hover', 'ithemeland-woocommerce-product-table-pro-lite'); ?></small>
        </label>
        <input type="text" iwptp-model-key="title" />
    </div>

    <?php include('condition/outer.php'); ?>
</div>

<div class="iwptp-element-settings-content-item" data-content="style">
    <div class="iwptp-editor-row-style-options" iwptp-model-key="style">
        <div iwptp-model-key="[id]">
            <!-- font-size -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Size', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="font-size">
            </div>

            <!-- font-color -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Stroke color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="color" placeholder="#000" class="iwptp-color-picker">
            </div>

            <!-- fill -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Fill color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="fill" class="iwptp-color-picker">
            </div>

            <!-- stroke-width -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Thickness', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="stroke-width">
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