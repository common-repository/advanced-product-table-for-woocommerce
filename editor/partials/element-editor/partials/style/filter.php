<div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="position" iwptp-condition-val="header">
    <div class="iwptp-editor-row-option" iwptp-model-key="style">

        <!-- Dropdown Heading -->
        <div class="iwptp-editor-row-option iwptp-toggle-options iwptp-row-accordion" iwptp-model-key=".iwptp-navigation:not(.iwptp-left-sidebar) [id].iwptp-dropdown.iwptp-filter > .iwptp-filter-heading">

            <span class="iwptp-toggle-label">
                <?php esc_html_e('Style: Dropdown heading', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
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

            <!-- font color:hover -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Font color hover', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="color:hover" placeholder="#000" class="iwptp-color-picker">
            </div>

            <!-- background color -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Background color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="background-color" class="iwptp-color-picker">
            </div>

            <!-- background color on hover -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Background color hover', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="background-color:hover" class="iwptp-color-picker">
            </div>

            <!-- border color -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Border color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="border-color" class="iwptp-color-picker">
            </div>

            <!-- border color on hover -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Border color hover', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="border-color:hover" class="iwptp-color-picker">
            </div>

            <!-- border radius -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Border radius', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="border-radius" />
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

        <!-- Dropdown Menu -->
        <div class="iwptp-editor-row-option iwptp-toggle-options iwptp-row-accordion" iwptp-model-key=".iwptp-navigation:not(.iwptp-left-sidebar) [id].iwptp-dropdown.iwptp-filter > .iwptp-dropdown-menu">

            <span class="iwptp-toggle-label">
                <?php esc_html_e('Style: Dropdown menu', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
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
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Font color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="color" placeholder="#000" class="iwptp-color-picker">
            </div>

            <!-- background color -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Background color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="background-color" class="iwptp-color-picker">
            </div>

            <!-- border -->
            <div class="iwptp-editor-row-option iwptp-borders-style">
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
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Border radius', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="border-radius" />
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


        <!-- Row Heading -->
        <div class="iwptp-editor-row-option iwptp-toggle-options iwptp-row-accordion" iwptp-model-key=".iwptp-navigation:not(.iwptp-left-sidebar) [id].iwptp-options-row.iwptp-filter > .iwptp-filter-heading">

            <span class="iwptp-toggle-label">
                <?php esc_html_e('Style: Row heading', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
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

        <!-- Row Options -->
        <div class="iwptp-editor-row-option iwptp-toggle-options iwptp-row-accordion" iwptp-model-key=".iwptp-navigation:not(.iwptp-left-sidebar) [id].iwptp-options-row.iwptp-filter > .iwptp-options > .iwptp-option">

            <span class="iwptp-toggle-label">
                <?php esc_html_e('Style: Row options', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
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
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Font color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="color" placeholder="#000" class="iwptp-color-picker">
            </div>

            <!-- font color:hover -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Font color hover', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="color:hover" placeholder="#000" class="iwptp-color-picker">
            </div>

            <!-- font color:selected -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Font color selected', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="color:selected" placeholder="#000" class="iwptp-color-picker">
            </div>

            <!-- background color -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Background color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="background-color" class="iwptp-color-picker">
            </div>

            <!-- background color:hover -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Bg color hover', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="background-color:hover" class="iwptp-color-picker">
            </div>

            <!-- background color:selected -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Bg color selected', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="background-color:selected" class="iwptp-color-picker">
            </div>

            <!-- border -->
            <div class="iwptp-editor-row-option iwptp-borders-style">
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

            <!-- border color:hover -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Border color hover', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="border-color:hover" class="iwptp-color-picker">
            </div>

            <!-- border color:selected -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Border color selected', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="border-color:selected" class="iwptp-color-picker">
            </div>

            <!-- border radius -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Border radius', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="border-radius" />
            </div>

            <!-- width -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Width', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="width" />
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