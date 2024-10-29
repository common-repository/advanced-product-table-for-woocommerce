<?php
if (empty($type)) {
    $type = 'manual_options';
}

if ($type == 'manual_options') {
    $model_key = 'manual_options';
    $controller = 'manual_options';
    $row_template = 'manual_option';
    $initial_data = 'manual_option';
    $label = 'Label';
    $compare_label = 'This option will select products with';
} else if ($type == 'relabel_rules') {
    $model_key = 'relabel_rules';
    $controller = 'relabel_rules';
    $row_template = 'relabel_rule';
    $initial_data = 'relabel_rule';
    $label = 'Relabel';
    $compare_label = 'Relabel for';
}
?>

<div class="iwptp-label-options-rows-wrapper iwptp-sortable iwptp-editor-row-option" iwptp-model-key="<?php echo esc_attr($model_key); ?>">
    <div class="iwptp-editor-row iwptp-editor-custom-label-setup" iwptp-controller="<?php echo esc_attr($controller); ?>" iwptp-model-key="[]" iwptp-model-key-index="0" iwptp-row-template="<?php echo esc_attr($row_template); ?>" iwptp-initial-data="<?php echo esc_attr($initial_data); ?>">

        <!-- compare -->
        <label><?php echo esc_html($compare_label); ?></label>
        <select iwptp-model-key="compare">
            <option value="="><?php esc_html_e('A specific custom field value', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="BETWEEN"><?php esc_html_e('Custom field values within a range', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
        </select>

        <!-- value -->
        <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="compare" iwptp-condition-val="=">
            <label><?php esc_html_e('Custom field value', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <input type="text" iwptp-model-key="value" />
        </div>

        <!-- min value -->
        <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="compare" iwptp-condition-val="BETWEEN">
            <label><?php esc_html_e('Custom field min value', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <input type="number" iwptp-model-key="min_value" />
        </div>

        <!-- max value -->
        <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="compare" iwptp-condition-val="BETWEEN">
            <label><?php esc_html_e('Custom field max value', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <input type="number" iwptp-model-key="max_value" />
        </div>

        <!-- label -->
        <div class="iwptp-editor-row-option">
            <label><?php echo esc_html($label); ?></label>
            <textarea type="text" iwptp-model-key="label"></textarea>
        </div>

        <?php
        if ($type == 'relabel_rules') {
        ?>
            <!-- style -->
            <div class="iwptp-editor-row-option" iwptp-model-key="style">

                <!-- color -->
                <div class="iwptp-editor-row-option">
                    <label><?php esc_html_e('Font color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                    <input type="text" iwptp-model-key="color" class="iwptp-color-picker" />
                </div>

                <!-- background color -->
                <div class="iwptp-editor-row-option">
                    <label><?php esc_html_e('Background color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                    <input type="text" iwptp-model-key="background-color" class="iwptp-color-picker" />
                </div>

            </div>
        <?php
        }
        ?>

        <!-- corner options -->
        <?php iwptp_corner_options(); ?>

    </div>

    <button class="iwptp-button" iwptp-add-row-template="<?php echo esc_attr($row_template); ?>">
        <?php esc_html_e('Add an Option', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
    </button>

</div>