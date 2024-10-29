<div class="iwptp-element-settings-content-item active" data-content="general">
    <div class="iwptp-editor-row-option">
        <label><?php esc_html_e('Type', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <div class="iwptp-diw">
            <select id="iwptp-editor-mini-cart-type" iwptp-model-key="mini_cart_type" data-need-to-change="true">
                <option value="default"><?php esc_html_e('Default', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                <option value="inline_mode"><?php esc_html_e('Inline mode', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                <option value="" disabled><?php esc_html_e('Float side [PRO]', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                <option value="" disabled><?php esc_html_e('Float toggle (Popup) [PRO]', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            </select>
            <div class="iwptp-diw-tray"></div>
        </div>
    </div>

    <div class="iwptp-editor-mini-cart-type-sub-fields">
        <div class="iwptp-for-default">
            <div class="iwptp-editor-row-option" style="padding-top: 15px;" data-name="toggle">
                <label><?php esc_html_e('Toggle', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <div class="iwptp-diw">
                    <select iwptp-model-key="toggle">
                        <option value="disabled"><?php esc_html_e('Disabled', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                        <option value="enabled"><?php esc_html_e('Enabled', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                    </select>
                    <div class="iwptp-diw-tray"></div>
                </div>
            </div>

            <div class="iwptp-editor-row-option" style="padding-top: 15px;" data-name="responsive_toggle">
                <label><?php esc_html_e('Responsive toggle', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <div class="iwptp-diw">
                    <select iwptp-model-key="r_toggle">
                        <option value="disabled"><?php esc_html_e('Disabled', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                        <option value="enabled"><?php esc_html_e('Enabled', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                    </select>
                    <div class="iwptp-diw-tray"></div>
                </div>
            </div>

            <div class="iwptp-editor-row-option" style="padding-top: 15px;" data-name="link_to">
                <label><?php esc_html_e('Link to', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <div class="iwptp-diw">
                    <select iwptp-model-key="link">
                        <option value=""></option>
                        <option value="cart"><?php esc_html_e('Cart', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                        <option value="checkout"><?php esc_html_e('Checkout', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                        <option value="custom_url"><?php esc_html_e('Custom url', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                    </select>
                    <div class="iwptp-diw-tray"></div>
                </div>
            </div>

            <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="link" iwptp-condition-val="custom_url">
                <label><?php esc_html_e('Custom redirect URL', 'ithemeland-woocommerce-product-table-pro-lite'); ?> </label>
                <div class="iwptp-diw">
                    <input type="text" iwptp-model-key="custom_url">
                    <div class="iwptp-diw-tray"></div>
                </div>
            </div>

            <div class="iwptp-editor-row-option" style="padding-top: 15px;" data-name="cost_source">
                <label><?php esc_html_e('Cost source', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <div class="iwptp-diw">
                    <select iwptp-model-key="cost_source">
                        <option value="total"><?php esc_html_e('Total', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                        <option value="subtotal"><?php esc_html_e('Subtotal', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                    </select>
                    <div class="iwptp-diw-tray"></div>
                </div>
            </div>

            <!-- Bottom offset (px) -->
            <div class="iwptp-editor-row-option" style="padding-top: 15px;" data-name="bottom_offset">
                <label><?php esc_html_e('Bottom offset (px)', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <div class="iwptp-diw">
                    <input type="number" iwptp-model-key="bottom_offset">
                    <div class="iwptp-diw-tray"></div>
                </div>
            </div>

            <!-- width (px) -->
            <div class="iwptp-editor-row-option" style="padding-top: 15px;" data-name="width">
                <label><?php esc_html_e('width (px)', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <div class="iwptp-diw">
                    <input type="number" iwptp-model-key="width">
                    <div class="iwptp-diw-tray"></div>
                </div>
            </div>
        </div>

        <!-- title -->
        <div class="iwptp-editor-row-option" style="padding-top: 15px;" data-name="title">
            <label><?php esc_html_e('Title', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <div class="iwptp-diw">
                <input type="text" iwptp-model-key="title">
                <div class="iwptp-diw-tray"></div>
            </div>
        </div>

        <!-- position -->
        <div class="iwptp-editor-row-option" data-name="position">
            <label><?php esc_html_e('Position', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <div class="iwptp-diw">
                <select iwptp-model-key="side_position">
                    <option value="right"><?php esc_html_e('Right', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                    <option value="left"><?php esc_html_e('Left', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                </select>
                <div class="iwptp-diw-tray"></div>
            </div>
        </div>

        <!-- hide on zero -->
        <div class="iwptp-editor-row-option" data-name="hide_on_zero">
            <label><?php esc_html_e('Hide on zero', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <div class="iwptp-diw">
                <select iwptp-model-key="hide_on_zero">
                    <option value="enable"><?php esc_html_e('Enable', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                    <option value="disable"><?php esc_html_e('Disable', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                </select>
                <div class="iwptp-diw-tray"></div>
            </div>
        </div>

        <!-- float position -->
        <div class="iwptp-editor-row-option" data-name="float_position">
            <label><?php esc_html_e('Float Position', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <div class="iwptp-diw">
                <select iwptp-model-key="float_position">
                    <option value="bottom_right"><?php esc_html_e('Bottom right', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                    <option value="bottom_left"><?php esc_html_e('Bottom left', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                    <option value="top_right"><?php esc_html_e('Top right', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                    <option value="top_left"><?php esc_html_e('Top left', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                </select>
                <div class="iwptp-diw-tray"></div>
            </div>
        </div>

        <!-- size -->
        <div class="iwptp-editor-row-option" data-name="button_size">
            <label><?php esc_html_e('Button Size', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <div class="iwptp-diw">
                <input type="number" iwptp-model-key="button_size">
                <div class="iwptp-diw-tray"></div>
            </div>
        </div>

        <!-- button text -->
        <div class="iwptp-editor-row-option" data-name="button_text">
            <label><?php esc_html_e('Button text', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <div class="iwptp-diw">
                <input type="text" iwptp-model-key="button_text">
                <div class="iwptp-diw-tray"></div>
            </div>
        </div>

        <!-- show total -->
        <div class="iwptp-editor-row-option" data-name="show_total">
            <label><?php esc_html_e('Show total', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <div class="iwptp-diw">
                <select iwptp-model-key="show_total">
                    <option value="enable"><?php esc_html_e('Enable', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                    <option value="disable"><?php esc_html_e('Disable', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                </select>
                <div class="iwptp-diw-tray"></div>
            </div>
        </div>

        <div class="iwptp-editor-row-option" data-name="subtotal">
            <label><?php esc_html_e('Subtotal', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <div class="iwptp-diw">
                <select iwptp-model-key="mini_cart_subtotal">
                    <option value="enable"><?php esc_html_e('Enable', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                    <option value="disable"><?php esc_html_e('Disable', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                </select>
                <div class="iwptp-diw-tray"></div>
            </div>
        </div>

        <div class="iwptp-editor-row-option" data-name="empty_cart_button">
            <label><?php esc_html_e('Empty cart button', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <div class="iwptp-diw">
                <select iwptp-model-key="empty_cart_button">
                    <option value="enable"><?php esc_html_e('Enable', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                    <option value="disable"><?php esc_html_e('Disable', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                </select>
                <div class="iwptp-diw-tray"></div>
            </div>
        </div>

        <div class="iwptp-editor-row-option" data-name="view_checkout_button">
            <label><?php esc_html_e('View checkout button', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <div class="iwptp-diw">
                <select iwptp-model-key="view_checkout_button">
                    <option value="enable"><?php esc_html_e('Enable', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                    <option value="disable"><?php esc_html_e('Disable', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                </select>
                <div class="iwptp-diw-tray"></div>
            </div>
        </div>

        <div class="iwptp-editor-row-option" data-name="view_cart_button">
            <label><?php esc_html_e('View cart button', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <div class="iwptp-diw">
                <select iwptp-model-key="view_cart_button">
                    <option value="enable"><?php esc_html_e('Enable', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                    <option value="disable"><?php esc_html_e('Disable', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                </select>
                <div class="iwptp-diw-tray"></div>
            </div>
        </div>
    </div>
</div>

<div class="iwptp-element-settings-content-item" data-content="style">
</div>