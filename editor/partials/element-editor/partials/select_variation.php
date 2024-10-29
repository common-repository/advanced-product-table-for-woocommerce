<div class="iwptp-element-settings-content-item active" data-content="general">

    <div class="iwptp-editor-row-option">
        <label>
            <?php esc_html_e('Display type', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
        <select class="" iwptp-model-key="display_type">
            <option value="dropdown"><?php esc_html_e('Dropdown with all variations', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="radio_multiple"><?php esc_html_e('Radio buttons for all variations', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="radio_single"><?php esc_html_e('Single radio button &mdash; for 1 variation', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
        </select>
    </div>

    <!-- single radio options -->
    <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="display_type" iwptp-condition-val="dropdown||radio_multiple">

        <div class="iwptp-editor-row-option">
            <label>
                <input type="checkbox" iwptp-model-key="hide_attributes">
                <?php esc_html_e('Hide attributes from variation name', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                <small>
                    <?php esc_html_e('Eg: "Size: Large, Gluten: Gluten free" becomes "Large, Gluten free"', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                </small>
            </label>
        </div>

        <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="hide_attributes" iwptp-condition-val="false">
            <label>
                <?php esc_html_e('Separator between each attribute and it\'s term', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                <small>
                    <?php esc_html_e('A character to show separation between the attribute and the term. Eg: : - &mdash;', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                </small>
            </label>
            <input type="text" iwptp-model-key="attribute_term_separator">
        </div>

        <div class="iwptp-editor-row-option">
            <label>
                <?php esc_html_e('Separator between different attributes', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                <small>
                    <?php esc_html_e('A character to show separation between the attributes. Eg: , | & ::', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                </small>
            </label>
            <input type="text" iwptp-model-key="attribute_separator">
        </div>

    </div>

    <!-- dropdown options -->
    <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="display_type" iwptp-condition-val="dropdown">

        <!-- hide select -->
        <div class="iwptp-editor-row-option">
            <label>
                <input type="checkbox" iwptp-model-key="hide_select" />
                <?php esc_html_e('Hide the \'Select\' option if default variation is available', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            </label>
        </div>

        <!-- select label -->
        <div class="iwptp-editor-row-option">
            <label>
                <?php esc_html_e('Label for the \'Select\' option', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                <small>
                    <?php esc_html_e('Appears when no default variation is selected', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                </small>
            </label>
            <input type="text" iwptp-model-key="select_label">
        </div>

    </div>

    <!-- radio options -->
    <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="display_type" iwptp-condition-val="radio_multiple">
        <div class="iwptp-editor-row-option">
            <label>
                <input type="checkbox" iwptp-model-key="separate_lines" />
                <?php esc_html_e('Show options in separate lines', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            </label>
        </div>
    </div>

    <!-- radio & dropdown options -->
    <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="display_type" iwptp-condition-val="radio_multiple||dropdown">

        <!-- hide stock -->
        <div class="iwptp-editor-row-option">
            <label>
                <input type="checkbox" iwptp-model-key="hide_stock" />
                <?php esc_html_e('Hide stock', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            </label>
        </div>

        <!-- hide price -->
        <div class="iwptp-editor-row-option">
            <label>
                <input type="checkbox" iwptp-model-key="hide_price" />
                <?php esc_html_e('Hide price', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            </label>
        </div>

        <!-- template for non-variable -->
        <div class="iwptp-editor-row-option">
            <label>
                <?php esc_html_e('Output template when product is not variable', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            </label>
            <div iwptp-model-key="non_variable_template" iwptp-block-editor="" iwptp-be-add-row="1" iwptp-be-add-element-partial="add-column-cell-element"></div>
        </div>

    </div>

    <!-- single radio options -->
    <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="display_type" iwptp-condition-val="radio_single">

        <div class="iwptp-editor-row-option">
            <label>
                <?php esc_html_e('Variation name', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                <small><?php esc_html_e('Give this variation a name', 'ithemeland-woocommerce-product-table-pro-lite'); ?></small>
            </label>
            <input type="text" iwptp-model-key="variation_name" />
        </div>

        <div class="iwptp-editor-row-option" iwptp-model-key="attribute_terms">

            <label>
                <?php esc_html_e('Specify all attribute-terms of this variation', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                <small><?php esc_html_e('This list will help IWPTPL identify the variation', 'ithemeland-woocommerce-product-table-pro-lite'); ?></small>
            </label>

            <div class="iwptp-editor-row iwptp-editor-select-variation-attribute-term" iwptp-controller="taxonomy_terms" iwptp-model-key="[]" iwptp-model-key-index="0" iwptp-row-template="identify_variation">
                <select iwptp-model-key="taxonomy">
                    <option value=""><?php esc_html_e('Attribute', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                    <?php
                    foreach ($attributes as $attribute) {
                        echo '<option value="pa_' . esc_attr($attribute->attribute_name) . '">' . esc_html($attribute->attribute_label) . '</option>';
                    }
                    ?>
                </select>
                <select iwptp-model-key="term">
                    <option value=""><?php esc_html_e('Term', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                </select>
                <span class="iwptp-loading-term" style="display: none;"><?php echo wp_kses(iwptp_icon('loader', 'iwptp-rotate'), iwptp_allowed_html_tags()); ?> <?php esc_html_e('Loading ...', 'ithemeland-woocommerce-product-table-pro-lite'); ?></span>
                <span class="iwptp-remove-item" iwptp-remove-row title="Delete row"><?php echo wp_kses(iwptp_icon('x'), iwptp_allowed_html_tags()); ?></span>
            </div>

            <button class="iwptp-button" iwptp-add-row-template="identify_variation">
                <?php esc_html_e('Add another', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            </button>

        </div>

        <div class="iwptp-editor-row-option">
            <label>
                <?php esc_html_e('Template', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                <small><?php esc_html_e('Placeholder', 'ithemeland-woocommerce-product-table-pro-lite'); ?>: [variation_name]</small>
            </label>
            <div iwptp-block-editor="" iwptp-be-add-element-partial="add-variation-element" iwptp-be-add-row="1" iwptp-model-key="template"></div>
        </div>

        <div class="iwptp-editor-row-option">
            <label>
                <?php esc_html_e('Output if this variation does not exist for the product', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                <small><?php esc_html_e('Leave empty for no output', 'ithemeland-woocommerce-product-table-pro-lite'); ?></small>
            </label>
            <div iwptp-block-editor="" iwptp-be-add-element-partial="add-common-element" iwptp-be-add-row="1" iwptp-model-key="not_exist_template"></div>
        </div>

    </div> <!-- /single radio options -->

    <?php include('condition/outer.php'); ?>
</div>

<div class="iwptp-element-settings-content-item" data-content="style">

    <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="display_type" iwptp-condition-val="radio_single">
        <div class="iwptp-editor-row-option" iwptp-model-key="style">
            <div class="iwptp-editor-row-option iwptp-toggle-options iwptp-row-accordion" iwptp-model-key="[id]">
                <span class="iwptp-toggle-label">
                    <?php esc_html_e('Style for Container', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                    <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?>
                </span>
                <?php require('style/common-props.php'); ?>
            </div>

            <div class="iwptp-editor-row-option iwptp-toggle-options iwptp-row-accordion" iwptp-model-key="[id].iwptp-variation-out-of-stock">
                <span class="iwptp-toggle-label">
                    <?php esc_html_e('Style when variation is \'Out of Stock\'', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                    <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?>
                </span>
                <?php require('style/common-props.php'); ?>
            </div>

            <div class="iwptp-editor-row-option iwptp-toggle-options iwptp-row-accordion" iwptp-model-key="[id].iwptp-selected">
                <span class="iwptp-toggle-label">
                    <?php esc_html_e('Style when variation is \'Selected\'', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                    <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?>
                </span>
                <?php require('style/common-props.php'); ?>
            </div>
        </div>
    </div>

    <div iwptp-panel-condition="prop" iwptp-condition-prop="display_type" iwptp-condition-val="dropdown">
        <div iwptp-model-key="style">
            <div class="iwptp-toggle-options iwptp-row-accordion" iwptp-model-key="[id] > .iwptp-select-variation-dropdown">
                <span class="iwptp-toggle-label">
                    <?php esc_html_e('Style for Dropdown', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                    <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?>
                </span>

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

    <div iwptp-panel-condition="prop" iwptp-condition-prop="display_type" iwptp-condition-val="radio_multiple">
        <div iwptp-model-key="style">
            <div class="iwptp-toggle-options iwptp-row-accordion" iwptp-model-key="[id].iwptp-select-variation-radio-multiple-wrapper">
                <span class="iwptp-toggle-label">
                    <?php esc_html_e('Style for Container', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                    <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?>
                </span>

                <!-- font-size -->
                <div class="iwptp-editor-row-option">
                    <label><?php esc_html_e('Font size', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                    <input type="text" iwptp-model-key="font-size" />
                </div>

                <!-- font color -->
                <div class="iwptp-editor-row-option">
                    <label><?php esc_html_e('Font color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                    <input type="text" iwptp-model-key="color" placeholder="#000" class="iwptp-color-picker">
                </div>

                <!-- line-height -->
                <div class="iwptp-editor-row-option">
                    <label><?php esc_html_e('Line height', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                    <input type="text" iwptp-model-key="line-height" placeholder="1.2em">
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

            <div class="iwptp-toggle-options iwptp-row-accordion" iwptp-model-key="[id].iwptp-select-variation-radio-multiple-wrapper .iwptp-select-variation">
                <span class="iwptp-toggle-label">
                    <?php esc_html_e('Style for Option', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                    <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?>
                </span>

                <!-- font-size -->
                <div class="iwptp-editor-row-option">
                    <label><?php esc_html_e('Font size', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                    <input type="text" iwptp-model-key="font-size" />
                </div>

                <!-- font color -->
                <div class="iwptp-editor-row-option">
                    <label><?php esc_html_e('Font color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                    <input type="text" iwptp-model-key="color" placeholder="#000" class="iwptp-color-picker">
                </div>

                <!-- line-height -->
                <div class="iwptp-editor-row-option">
                    <label><?php esc_html_e('Line height', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                    <input type="text" iwptp-model-key="line-height" placeholder="1.2em">
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
</div>