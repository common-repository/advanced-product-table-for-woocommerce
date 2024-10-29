<div class="iwptp-element-settings-content-item active" data-content="general">
    <!-- clear all label -->
    <div class="iwptp-editor-row-option">
        <label>
            <?php esc_html_e('Limit word count', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
        <input type="number" iwptp-model-key="limit" />
    </div>

    <!-- truncation symbol -->
    <div class="iwptp-editor-row-option">
        <label>
            <?php esc_html_e('Truncation symbol (…)', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
        <label>
            <input type="radio" iwptp-model-key="truncation_symbol" value="">
            <?php esc_html_e('Keep it', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
        <label>
            <input type="radio" iwptp-model-key="truncation_symbol" value="hide">
            <?php esc_html_e('Hide it', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
        <label>
            <input type="radio" iwptp-model-key="truncation_symbol" value="custom">
            <?php esc_html_e('Enter custom symbol', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
    </div>

    <!-- truncation symbol: custom -->
    <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="truncation_symbol" iwptp-condition-val="custom">
        <label>
            <?php esc_html_e('Enter custom truncation symbol', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
        <input type="text" iwptp-model-key="custom_truncation_symbol" />
    </div>

    <!-- 'Read more' label -->
    <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="toggle_enabled" iwptp-condition-val="false">
        <label>
            <?php esc_html_e('\'Read more\' label', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            <small><?php esc_html_e('Leave empty to hide \'read more\' link', 'ithemeland-woocommerce-product-table-pro-lite'); ?></small>
        </label>
        <div iwptp-model-key="read_more_label" iwptp-block-editor="" iwptp-be-add-row="0"></div>
    </div>

    <!-- enable toggle -->
    <div class="iwptp-editor-row-option">
        <?php iwptp_checkbox(true, 'Enable toggle (show more / less)', "toggle_enabled"); ?>
    </div>

    <!-- toggle labels -->
    <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="toggle_enabled" iwptp-condition-val="true">
        <!-- show more label -->
        <div class="iwptp-editor-row-option">
            <label><?php esc_html_e('Show more label', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <div iwptp-block-editor iwptp-model-key="show_more_label" iwptp-be-add-row="0"></div>
        </div>

        <!-- show less label -->
        <div class="iwptp-editor-row-option">
            <label><?php esc_html_e('Show less label', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <div iwptp-block-editor iwptp-model-key="show_less_label" iwptp-be-add-row="0"></div>
        </div>
    </div>

    <!-- enable toggle -->
    <div class="iwptp-editor-row-option">
        <label>
            <?php esc_html_e('Action on shortcodes in content', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
        <label>
            <input type="radio" iwptp-model-key="shortcode_action" value="">
            <?php esc_html_e('Process once at end of table creation (efficient, default)', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
        <label>
            <input type="radio" iwptp-model-key="shortcode_action" value="strip">
            <?php esc_html_e('Remove all shortcodes from the content before printing', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
        <label>
            <input type="radio" iwptp-model-key="shortcode_action" value="process">
            <?php esc_html_e('Process under individual product context', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
    </div>

    <?php include('html-class.php'); ?>

    <?php include('condition/outer.php'); ?>
</div>

<div class="iwptp-element-settings-content-item" data-content="style">
    <div class="iwptp-editor-row-option" iwptp-model-key="style">

        <div iwptp-model-key="[id]">
            <!-- font-size -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Font size', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="font-size" />
            </div>

            <!-- line-height -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Line height', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="line-height" placeholder="1.2em">
            </div>

            <!-- font color -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Font color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="color" placeholder="#000" class="iwptp-color-picker">
            </div>

            <!-- font-family -->
            <div class="iwptp-editor-row-option">
                <label>
                    <?php esc_html_e('Font family', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                    <span class="iwptp-font-family-sample-text"><?php esc_html_e('Sample Text', 'ithemeland-woocommerce-product-table-pro-lite'); ?></span>
                </label>
                <select iwptp-model-key="font-family" class="iwptp-select2 iwptp-font-family-dropdown">
                    <?php include IWPTPL_PLUGIN_PATH . "editor/partials/element-editor/partials/font-family/font-family-options.php"; ?>
                </select>
            </div>

            <!-- width -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Width', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="width" />
            </div>

            <!-- max-width -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Max width', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="max-width" />
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
    </div>
</div>