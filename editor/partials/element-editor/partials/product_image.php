<div class="iwptp-element-settings-content-item active" data-content="general">
    <!-- size -->
    <div class="iwptp-editor-row-option">
        <label>
            <?php esc_html_e('Select image file size', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
        <select iwptp-model-key="size">
            <?php
            foreach (iwptp_get_all_image_sizes() as $image_size => $details) {
                echo "<option value='" . esc_attr($image_size) . "'>";
                echo wp_kses(ucfirst(str_replace('_', ' ', $image_size)), iwptp_allowed_html_tags()) . " (";
                $_details = "";
                if ($details['width']) {
                    $_details .= "w: " . $details['width'] . "px | ";
                }
                if ($details['height']) {
                    $_details .= "h: " . $details['height'] . "px | ";
                }
                $_details .= " cropped: " . ($details['crop'] ? "true" : "false") . " | ";
                echo esc_attr(rtrim($_details, " | "));
                echo ")</option>";
            }
            ?>
        </select>
    </div>

    <!-- enable placeholder -->
    <div class="iwptp-editor-row-option">
        <label>
            <input type="checkbox" iwptp-model-key="placeholder_enabled">
            <?php esc_html_e('Display placeholder if the image is not available', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
    </div>

    <!-- hover switch -->
    <div class="iwptp-editor-row-option">
        <?php iwptp_checkbox(true, 'Switch to first gallery image on hover', 'hover_switch_enabled'); ?>
    </div>

    <!-- image count -->
    <div class="iwptp-editor-row-option">
        <?php iwptp_checkbox(true, 'Display image gallery count in corner', 'image_count_enabled'); ?>
    </div>

    <!-- click action -->
    <div class="iwptp-editor-row-option">
        <label>
            <?php esc_html_e('Action on click', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
        <select iwptp-model-key="click_action">
            <option value=""><?php esc_html_e('Do nothing', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="product_page"><?php esc_html_e('Open product page', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="product_page_new"><?php esc_html_e('Open product page in a new tab', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="image_page_new"><?php esc_html_e('Show full size image in a new tab', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <?php iwptp_option('lightbox', __('Display image in lightbox', 'ithemeland-woocommerce-product-table-pro-lite')); ?>
            <?php iwptp_option('download', __('Download image', 'ithemeland-woocommerce-product-table-pro-lite')); ?>
        </select>
    </div>

    <!-- icon when -->
    <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="click_action" iwptp-condition-val="lightbox">
        <label>
            <?php esc_html_e('Show lightbox icon when', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
        <select iwptp-model-key="icon_when">
            <option value="always"><?php esc_html_e('Always', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="row_hover"><?php esc_html_e('Row is hovered upon', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="image_hover"><?php esc_html_e('Image is hovered upon', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="image_hover_hide"><?php esc_html_e('Hide when image is hovered upon', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="never"><?php esc_html_e('Never', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
        </select>
    </div>

    <!-- icon position -->
    <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="click_action" iwptp-condition-val="lightbox">
        <label>
            <?php esc_html_e('Lightbox icon position', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
        <select iwptp-model-key="icon_position">
            <option value="bottom_right"><?php esc_html_e('Bottom right', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="outside_right"><?php esc_html_e('Outside right', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
        </select>
    </div>

    <!-- zoom trigger -->
    <div class="iwptp-editor-row-option">
        <label>
            <?php esc_html_e('Zoom image when', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
        <div class="">
            <select iwptp-model-key="zoom_trigger">
                <option value=""><?php esc_html_e('Never', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                <option value="row_hover"><?php esc_html_e('Row is hovered upon', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                <option value="image_hover"><?php esc_html_e('Image is hovered upon', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            </select>
        </div>
    </div>

    <!-- zoom scale -->
    <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="zoom_trigger" iwptp-condition-val="row_hover||image_hover">
        <label>
            <?php esc_html_e('Zoom scale level', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
        <select iwptp-model-key="zoom_scale">
            <option value="1.05">1.05x</option>
            <option value="1.25">1.25x</option>
            <option value="1.5">1.5x</option>
            <option value="1.75">1.75x</option>
            <option value="2.0">2.0x</option>
            <option value="2.25">2.25x</option>
            <option value="2.5">2.5x</option>
            <option value="2.75">2.75x</option>
            <option value="3.0">3.0x</option>
            <option value="custom"><?php esc_html_e('Custom', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
        </select>
    </div>

    <!-- zoom scale -->
    <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="zoom_scale" iwptp-condition-val="custom">
        <label>
            <?php esc_html_e('Custom zoom scale level', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            <small><?php esc_html_e('Enter a decimal value like 3.0 without any alphabets.', 'ithemeland-woocommerce-product-table-pro-lite'); ?></small>
        </label>
        <input type="text" iwptp-model-key="custom_zoom_scale" />
    </div>

    <!-- offset zoom enabled -->
    <div class="iwptp-editor-row-option">
        <?php iwptp_checkbox(true, 'Show an offset, enlarged version of image on hover', 'offset_zoom_enabled'); ?>
    </div>

    <?php include('html-class.php'); ?>

    <?php include('condition/outer.php'); ?>
</div>

<div class="iwptp-element-settings-content-item" data-content="style">
    <div class="iwptp-editor-row-option">
        <div iwptp-model-key="style">
            <div class="iwptp-editor-row-option iwptp-toggle-options iwptp-row-accordion iwptp-open" iwptp-model-key="[id]">

                <span class="iwptp-toggle-label">
                    <?php esc_html_e('Style for Product Image', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                    <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?>
                </span>

                <!-- max-width -->
                <div class="iwptp-editor-row-option">
                    <label>
                        <?php esc_html_e('Width', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                        <small><?php esc_html_e('Use this option to set the image display width.', 'ithemeland-woocommerce-product-table-pro-lite'); ?></small>
                    </label>
                    <input type="text" iwptp-model-key="max-width" />
                </div>

                <!-- border -->
                <div class="iwptp-editor-row-option iwptp-borders-style">
                    <label><?php esc_html_e('Border', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                    <input type="text" iwptp-model-key="border-width" placeholder="<?php esc_html_e('width', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
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

    <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="click_action" iwptp-condition-val="lightbox">
        <div iwptp-model-key="style">
            <div class="iwptp-editor-row-option iwptp-toggle-options iwptp-row-accordion" iwptp-model-key="[id] > .iwptp-lightbox-icon">

                <span class="iwptp-toggle-label">
                    <?php esc_html_e('Style for LightBox Icon', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                    <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?>
                </span>

                <!-- color -->
                <div class="iwptp-editor-row-option">
                    <label><?php esc_html_e('Color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                    <input type="text" iwptp-model-key="color" />
                </div>

                <!-- background-color -->
                <div class="iwptp-editor-row-option">
                    <label><?php esc_html_e('Background color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                    <input type="text" iwptp-model-key="background-color" />
                </div>

                <!-- size -->
                <div class="iwptp-editor-row-option">
                    <label><?php esc_html_e('Size (px)', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                    <input type="number" iwptp-model-key="font-size" />
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

                <!-- border-radius -->
                <div class="iwptp-editor-row-option">
                    <label><?php esc_html_e('Border radius', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                    <input type="text" iwptp-model-key="border-radius">
                </div>

            </div>
        </div>
    </div>

    <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="offset_zoom_enabled" iwptp-condition-val="true">
        <div iwptp-model-key="style">
            <div class="iwptp-editor-row-option iwptp-toggle-options iwptp-row-accordion" iwptp-model-key="[id]--offset-zoom-image">

                <span class="iwptp-toggle-label">
                    <?php esc_html_e('Style for Offset Zoom Image', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                    <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?>
                </span>

                <!-- max-width -->
                <div class="iwptp-editor-row-option">
                    <label>
                        <?php esc_html_e('Max width', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                        <small><?php esc_html_e('Image width can be smaller but will never exceed this value', 'ithemeland-woocommerce-product-table-pro-lite'); ?></small>
                    </label>
                    <input type="text" iwptp-model-key="max-width" />
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

                <!-- border-radius -->
                <div class="iwptp-editor-row-option">
                    <label><?php esc_html_e('Border radius', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                    <input type="text" iwptp-model-key="border-radius">
                </div>

                <!-- background-color -->
                <div class="iwptp-editor-row-option">
                    <label><?php esc_html_e('Background color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                    <input type="text" iwptp-model-key="background-color" />
                </div>

                <!-- padding -->
                <div class="iwptp-editor-row-option">
                    <label><?php esc_html_e('Padding', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                    <input type="text" iwptp-model-key="padding-top" placeholder="<?php esc_attr_e('top', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
                    <input type="text" iwptp-model-key="padding-right" placeholder="<?php esc_attr_e('right', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
                    <input type="text" iwptp-model-key="padding-bottom" placeholder="<?php esc_attr_e('bottom', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
                    <input type="text" iwptp-model-key="padding-left" placeholder="<?php esc_attr_e('left', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
                </div>
            </div>
        </div>
    </div>
</div>