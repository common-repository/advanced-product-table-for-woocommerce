<div class="iwptp-editor-row-option" iwptp-model-key="condition">
    <!-- custom field condition -->
    <div class="iwptp-editor-row-option">
        <select iwptp-model-key="action">
            <option value="show"><?php esc_html_e('Show element only if ALL conditions are met', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="hide"><?php esc_html_e('Hide element only if ALL conditions are met', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="show_any"><?php esc_html_e('Show element if ANY condition is met', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="hide_any"><?php esc_html_e('Hide element if ANY condition is met', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
        </select>
    </div>

    <!-- custom field condition -->
    <div class="iwptp-editor-row-option">
        <label>
            <input type="checkbox" iwptp-model-key="custom_field_enabled">
            <?php esc_html_e('Add custom field condition', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
    </div>

    <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="custom_field_enabled" iwptp-condition-val="true" style="margin-bottom: 30px;">

        <div class="iwptp-editor-row-option">
            <label>
                <?php esc_html_e('Custom field name', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            </label>
            <input type="text" iwptp-model-key="custom_field">
        </div>

        <div class="iwptp-editor-row-option">
            <label>
                Permitted values to meet condition
                <small>Any value will do: <i>leave empty</i></small>
                <small>Single permitted value: <i>value 1</i></small>
                <small>Multiple permitted values: <i>value 1 || value 2 || value 3</i></small>
                <small>Range of permitted numeric values: <i>150 - 600</i></small>
                <small>No value should be set: <i>-</i></small>
            </label>
            <input type="text" iwptp-model-key="custom_field_value">
        </div>

    </div>

    <!-- attribute condition -->
    <div class="iwptp-editor-row-option">
        <label>
            <input type="checkbox" iwptp-model-key="attribute_enabled">
            <?php esc_html_e('Add attribute condition', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
    </div>

    <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="attribute_enabled" iwptp-condition-val="true" style="margin-bottom: 30px;">

        <div class="iwptp-editor-row-option">
            <label><?php esc_html_e('Attribute', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <?php
            $attributes = wc_get_attribute_taxonomies();
            if (empty($attributes)) {
                echo '<div class="iwptp-notice">There are no WooCommerce attributes on this site!</div>';
                $hide_class = 'iwptp-hide';
            }
            ?>
            <select class="<?php echo empty($attributes) ? 'iwptp-hide' : '';  ?>" iwptp-model-key="attribute">
                <option value=""></option>
                <?php
                foreach ($attributes as $attribute) {
                ?>
                    <option value="<?php echo esc_attr($attribute->attribute_name); ?>">
                        <?php echo esc_html($attribute->attribute_label); ?>
                    </option>
                <?php
                }
                ?>
            </select>
        </div>

        <div class="iwptp-editor-row-option">
            <label>
                Term slugs
                <small>Any term will do: <i>leave empty</i></small>
                <small>Single permitted term: <i>term-1</i></small>
                <small>Multiple permitted terms: <i>term-1 || term-2 || term-3</i></small>
                <small>No term should be associated: <i>-</i></small>
            </label>
            <input type="text" iwptp-model-key="attribute_term">
        </div>

    </div>

    <!-- category condition -->
    <div class="iwptp-editor-row-option">
        <label>
            <input type="checkbox" iwptp-model-key="category_enabled">
            <?php esc_html_e('Add category condition', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
    </div>

    <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="category_enabled" iwptp-condition-val="true" style="margin-bottom: 30px;">

        <div class="iwptp-editor-row-option">
            <label>
                Category slugs
                <small>
                    Enter multiple possible categories with || separator: <br />
                    <i>category-1 || category-2 || category-3</i>
                </small>
            </label>
            <input type="text" iwptp-model-key="category" />
        </div>

    </div>

    <!-- price condition -->
    <div class="iwptp-editor-row-option">
        <label>
            <input type="checkbox" iwptp-model-key="price_enabled">
            <?php esc_html_e('Add price condition', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>

        <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="price_enabled" iwptp-condition-val="true" style="margin-bottom: 30px;">

            <div class="iwptp-editor-row-option">
                <label>
                    Acceptable price / price-range
                    <small>
                        Does not apply to variable products currently.
                        Range from 150 to 600: <i>150 - 600</i><br>
                        For less than 150: <i>0 - 150</i><br>
                        For greater than 150: <i>150 - 10000</i><br>
                    </small>
                </label>
                <input type="text" iwptp-model-key="price">
            </div>

        </div>
    </div>

    <!-- stock condition -->
    <div class="iwptp-editor-row-option">
        <label>
            <input type="checkbox" iwptp-model-key="stock_enabled">
            <?php esc_html_e('Add stock condition', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>

        <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="stock_enabled" iwptp-condition-val="true" style="margin-bottom: 30px;">

            <div class="iwptp-editor-row-option">
                <label>
                    Acceptable stock quantity / stock range
                    <small>Stock range: <i>4 - 8</i></small>
                    <small>Negative stock range (backorder): <i>-4 - 10</i></small>
                    <small>Multiple permitted options: <i>4 || 8 || 12</i></small>
                    <small>In stock: <i>instock</i></small>
                    <small>Out of stock: <i>outofstock</i></small>
                    <small>On backorder: <i>onbackorder</i></small>
                </label>
                <input type="text" iwptp-model-key="stock">
            </div>

        </div>
    </div>

    <!-- product type condition -->
    <div class="iwptp-editor-row-option">
        <label>
            <input type="checkbox" iwptp-model-key="product_type_enabled">
            <?php esc_html_e('Add stock condition', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>

        <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="product_type_enabled" iwptp-condition-val="true" style="margin-bottom: 30px;">
            <div class="iwptp-editor-row-option">
                <label>
                    <?php esc_html_e('Product types:', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                </label>
                <?php
                foreach (wc_get_product_types() as $product_type => $label) {
                ?>
                    <label class="iwptp-editor-checkbox-label">
                        <input type="checkbox" value="<?php echo esc_attr(strtolower($product_type)); ?>" iwptp-model-key="product_type[]">
                        <?php echo esc_html(str_replace(' product', '', $label)); ?>
                    </label>
                <?php
                }
                ?>
            </div>

        </div>
    </div>

    <!-- store timings condition -->
    <div class="iwptp-editor-row-option">
        <label>
            <input type="checkbox" iwptp-model-key="store_timings_enabled">
            <?php esc_html_e('Add store timings condition', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>

        <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="store_timings_enabled" iwptp-condition-val="true" style="margin-bottom: 30px;">

            <div class="iwptp-editor-row-option">
                <label>
                    <?php esc_html_e('Select the shop\'s timezone', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                </label>
                <select iwptp-model-key="timezone">
                    <?php echo wp_timezone_choice('UTC+0', get_user_locale()); ?>
                </select>
            </div>

            <div class="iwptp-editor-row-option">
                <label>
                    <small>
                        Enter only one day-timings rule per line<br>
                        Multiple timeslots for the same day are separated by "|"<br>
                        You can also use "[open_all_day]" and "[closed_all_day]"<br>
                        To target specific dates enter in the format: "month date, year: timings" <br>
                        <br>
                        monday: 1000 - 1400 | 1600 - 2000<br>
                        tuesday: 1000 - 1400 | 1600 - 2000<br>
                        wednesday: 1000 - 1400 | 1600 - 2000<br>
                        thursday: 1000 - 1400 | 1600 - 2000<br>
                        friday: 1000 - 1400 | 1600 - 2000<br><br>
                        saturday: [open_all_day]<br>
                        sunday: [closed_all_day]<br><br>
                        December 31, 2000: [closed_all_day]<br>
                        January 1, 2001: [open_all_day]<br>
                        January 2, 2001: 1000 - 1400 | 1600 - 2000<br>
                    </small>
                </label>
                <textarea iwptp-model-key="store_timings" style="height: 200px; width: 100%;"></textarea>
            </div>

        </div>

    </div>

    <!-- user role -->
    <div class="iwptp-editor-row-option">
        <label>
            <input type="checkbox" iwptp-model-key="user_role_enabled">
            <?php esc_html_e('Add user role condition', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>

        <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="user_role_enabled" iwptp-condition-val="true" style="margin-bottom: 30px;">
            <div class="iwptp-editor-row-option">
                <label>
                    <?php esc_html_e('User roles:', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                </label>
                <?php
                global $wp_roles;
                $user_roles = array('_visitor' => 'Guest') + $wp_roles->get_names();

                foreach ($user_roles as $role => $label) {
                ?>
                    <label class="iwptp-editor-checkbox-label">
                        <input type="checkbox" value="<?php echo esc_attr(strtolower($role)); ?>" iwptp-model-key="user_role[]">
                        <?php echo esc_html($label); ?>
                    </label>
                <?php
                }
                ?>
            </div>

        </div>
    </div>

</div>