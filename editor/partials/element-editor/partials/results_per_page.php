<div class="iwptp-element-settings-content-item active" data-content="general">
    <!-- display type -->
    <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="position" iwptp-condition-val="header">
        <label><?php esc_html_e('Display type', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <select iwptp-model-key="display_type">
            <option value="dropdown"><?php esc_html_e('Dropdown', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="row"><?php esc_html_e('Row', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
        </select>
    </div>

    <!-- heading -->
    <div class="iwptp-editor-row-option">
        <label>
            <?php esc_html_e('Heading', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            <small><?php esc_html_e('Optional. You may use [limit] for current setting\'s max posts per page.', 'ithemeland-woocommerce-product-table-pro-lite'); ?></small>
        </label>
        <input type="text" iwptp-model-key="heading">
    </div>

    <div class="iwptp-editor-row-option">
        <label class="iwptp-editor-options-heading"><?php esc_html_e('Options', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
    </div>

    <!-- options -->
    <div class="iwptp-editor-row-option">

        <!-- option rows -->
        <div class="iwptp-label-options-rows-wrapper" iwptp-model-key="dropdown_options">
            <div class="iwptp-editor-row iwptp-editor-custom-label-setup" iwptp-controller="results_per_page_options" iwptp-model-key="[]" iwptp-model-key-index="0" iwptp-initial-data="results_per_page_option" iwptp-row-template="results_per_page_option">

                <!-- label -->
                <div class="iwptp-editor-row-option">
                    <label><?php esc_html_e('Label', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                    <input type="text" iwptp-model-key="label">
                </div>

                <!-- results -->
                <div class="iwptp-editor-row-option">
                    <label><?php esc_html_e('Results', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                    <input type="number" iwptp-model-key="results" />
                </div>

                <!-- corner options -->
                <?php iwptp_corner_options(); ?>

            </div>

            <button class="iwptp-button" iwptp-add-row-template="results_per_page_option">
                <?php esc_html_e('Add an Option', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            </button>

        </div>
    </div>

    <!-- accordion always open -->
    <div class="iwptp-editor-row-option">
        <label>
            <input type="checkbox" iwptp-model-key="accordion_always_open"> <?php esc_html_e('Keep filter open by default if it is in sidebar', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
    </div>
</div>

<div class="iwptp-element-settings-content-item" data-content="style">
    <?php include('style/filter.php'); ?>
</div>