<div class="iwptp-nav-device" iwptp-controller="laptop_navigation" iwptp-model-key="laptop">

    <div class="iwptp-editor-light-heading">
        <?php esc_html_e('Laptop navigation', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
    </div>
    <div class="iwptp-clear"></div>

    <div class="iwptp-navigation-errors" style="display: none;">
        <strong class="iwptp-navigation-errors__heading"><?php esc_html_e('Warning(s)', 'ithemeland-woocommerce-product-table-pro-lite'); ?></strong>
        <ul class="iwptp-navigation-errors__warnings"></ul>
    </div>

    <!-- left sidebar -->
    <div class="iwptp-left-sidebar-settings">
        <div class="iwptp-navigation-left-sidebar-settings">
            <div class="iwptp-editor-light-heading iwptp-sub">
                <?php esc_html_e('Sidebar Settings', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            </div>
            <div class="iwptp-navigation-left-sidebar-settings-item">
                <label><?php esc_html_e('Sidebar position', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <select id="iwptp-editor-navigation-sidebar-position" iwptp-model-key="sidebar_position" disabled>
                    <option value="disable"><?php esc_html_e('Disable', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                </select>
            </div>
        </div>

        <div class="iwptp-editor-light-heading iwptp-sub">
            <?php esc_html_e('Sidebar Elements', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </div>
        <div class="iwptp-left-sidebar-pro-link" style="margin: 0; font-size: 13px;">
            <p>All of elements are available on Premium Version</p>
            <a href="https://ithemelandco.com/woocommerce-product-table-pro?utm_source=free_plugins&utm_medium=plugin_links&utm_campaign=user-lite-buy" target="_blank">Get Premium</a>
        </div>
    </div>

    <!-- header -->
    <div class="iwptp-header-settings" iwptp-model-key="header">

        <div class="iwptp-editor-light-heading iwptp-sub">
            <?php esc_html_e('Header', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </div>

        <!-- navigation: header rows wrapper -->
        <div class="iwptp-sortable iwptp-header-rows-wrapper" iwptp-controller="header_rows" iwptp-model-key="rows">
            <!-- header row -->
            <div class="iwptp-nav-editor-row" iwptp-controller="nav_header_row" iwptp-model-key="[]" iwptp-model-key-index="0" iwptp-row-template="nav_header_row" iwptp-initial-data="nav_header_row">

                <!-- columns enabled -->
                <select iwptp-model-key="ratio">
                    <option value="100-0"><?php esc_html_e('Only Left column', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                    <option value="70-30"><?php esc_html_e('Left: 70% | Right: 30%', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                    <option value="50-50"><?php esc_html_e('Left: 50% | Right: 50%', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                    <option value="30-70"><?php esc_html_e('Left: 30% | Right: 70%', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                    <option value="0-100"><?php esc_html_e('Only Right column', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                </select>

                <?php iwptp_corner_options(); ?>


                <!-- textarea options -->
                <div class="iwptp-editor-row-option iwptp-editor-filter-row-options iwptp-editor-header-textareas-container" iwptp-model-key="columns">

                    <!-- left column -->
                    <div class="iwptp-editor-left-column-input-wrapper iwptp-editor-header-textarea-wrapper" iwptp-model-key="left">
                        <div iwptp-model-key="template" iwptp-block-editor iwptp-be-add-element-partial="add-navigation-header-element" iwptp-be-connect-with=".iwptp-editor-tab-navigation [iwptp-model-key='laptop'] .iwptp-block-editor-row" iwptp-be-add-row="0"></div>
                    </div>

                    <!-- center column -->
                    <div class="iwptp-editor-center-column-input-wrapper iwptp-editor-header-textarea-wrapper" iwptp-model-key="center">
                        <div iwptp-model-key="template" iwptp-block-editor iwptp-be-add-element-partial="add-navigation-header-element" iwptp-be-connect-with=".iwptp-editor-tab-navigation [iwptp-model-key='laptop'] .iwptp-block-editor-row" iwptp-be-add-row="0"></div>
                    </div>

                    <!-- right column -->
                    <div class="iwptp-editor-right-column-input-wrapper iwptp-editor-header-textarea-wrapper" iwptp-model-key="right">
                        <div iwptp-model-key="template" iwptp-block-editor iwptp-be-add-element-partial="add-navigation-header-element" iwptp-be-connect-with=".iwptp-editor-tab-navigation [iwptp-model-key='laptop'] .iwptp-block-editor-row" iwptp-be-add-row="0"></div>
                    </div>

                </div>

            </div>
            <!-- /header row -->

            <button class="iwptp-button iwptp-add-header_row" iwptp-add-row-template="nav_header_row">
                <?php esc_html_e('Add Header Row', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            </button>

        </div>
        <!-- /navigation: header rows wrapper -->

    </div>

    <!-- footer -->
    <div class="iwptp-footer-settings" iwptp-model-key="footer">

        <div class="iwptp-editor-light-heading iwptp-sub">
            <?php esc_html_e('Footer', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </div>

        <!-- navigation: footer rows wrapper -->
        <div class="iwptp-sortable iwptp-footer-rows-wrapper" iwptp-controller="footer_rows" iwptp-model-key="rows">
            <!-- footer row -->
            <div class="iwptp-nav-editor-row" iwptp-controller="nav_footer_row" iwptp-model-key="[]" iwptp-model-key-index="0" iwptp-row-template="nav_footer_row" iwptp-initial-data="nav_footer_row">

                <!-- columns enabled -->
                <select iwptp-model-key="ratio">
                    <option value="100-0"><?php esc_html_e('Only Left column', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                    <option value="70-30"><?php esc_html_e('Left: 70% | Right: 30%', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                    <option value="50-50"><?php esc_html_e('Left: 50% | Right: 50%', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                    <option value="30-70"><?php esc_html_e('Left: 30% | Right: 70%', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                    <option value="0-100"><?php esc_html_e('Only Right column', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                </select>

                <?php iwptp_corner_options(); ?>

                <!-- textarea options -->
                <div class="iwptp-editor-row-option iwptp-editor-filter-row-options iwptp-editor-footer-textareas-container" iwptp-model-key="columns">

                    <!-- left column -->
                    <div class="iwptp-editor-left-column-input-wrapper iwptp-editor-footer-textarea-wrapper" iwptp-model-key="left">
                        <div iwptp-model-key="template" iwptp-block-editor iwptp-be-add-element-partial="add-navigation-footer-element" iwptp-be-connect-with=".iwptp-editor-tab-navigation [iwptp-model-key='laptop'] .iwptp-block-editor-row" iwptp-be-add-row="0"></div>
                    </div>

                    <!-- center column -->
                    <div class="iwptp-editor-center-column-input-wrapper iwptp-editor-footer-textarea-wrapper" iwptp-model-key="center">
                        <div iwptp-model-key="template" iwptp-block-editor iwptp-be-add-element-partial="add-navigation-footer-element" iwptp-be-connect-with=".iwptp-editor-tab-navigation [iwptp-model-key='laptop'] .iwptp-block-editor-row" iwptp-be-add-row="0"></div>
                    </div>

                    <!-- right column -->
                    <div class="iwptp-editor-right-column-input-wrapper iwptp-editor-footer-textarea-wrapper" iwptp-model-key="right">
                        <div iwptp-model-key="template" iwptp-block-editor iwptp-be-add-element-partial="add-navigation-footer-element" iwptp-be-connect-with=".iwptp-editor-tab-navigation [iwptp-model-key='laptop'] .iwptp-block-editor-row" iwptp-be-add-row="0"></div>
                    </div>

                </div>

            </div>
            <!-- /footer row -->

            <button class="iwptp-button iwptp-add-footer_row" iwptp-add-row-template="nav_footer_row">
                <?php esc_html_e('Add Footer Row', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            </button>

        </div>
        <!-- /navigation: header rows wrapper -->

    </div>
</div>

<div class="iwptp-nav-device">
    <div class="iwptp-editor-light-heading">
        <?php esc_html_e('Responsive navigation', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
    </div>

    <div class="iwptp-nav-inherit-notice"><?php esc_html_e('Leave empty to inherit Laptop navigation', 'ithemeland-woocommerce-product-table-pro-lite'); ?></div>

    <div iwptp-model-key="phone" iwptp-block-editor iwptp-be-add-row='1' iwptp-be-add-element-partial="add-responsive-navigation-element" iwptp-be-connect-with=".iwptp-editor-tab-navigation [iwptp-model-key='phone'] .iwptp-block-editor-row"></div>
</div>