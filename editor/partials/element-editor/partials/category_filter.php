<div class="iwptp-element-settings-content-item active" data-content="general">

    <input type="hidden" iwptp-model-key="taxonomy" value="product_cat" />

    <!-- heading -->
    <div class="iwptp-editor-row-option">
        <label><?php esc_html_e('Heading', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <div iwptp-block-editor iwptp-be-add-element-partial="add-navigation-filter-heading-element" iwptp-model-key="heading" iwptp-be-add-row="0"></div>
    </div>

    <!-- display type -->
    <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="position" iwptp-condition-val="header">
        <label><?php esc_html_e('Display type', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <select iwptp-model-key="display_type">
            <option value="dropdown"><?php esc_html_e('Dropdown', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="row"><?php esc_html_e('Row', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
        </select>
    </div>

    <!-- multiple selections permission -->
    <div class="iwptp-editor-row-option">
        <label>
            <input type="checkbox" iwptp-model-key="single" />
            <?php esc_html_e('Only allow one option to be selected', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
    </div>

    <!-- heading format upon option selection -->
    <?php require('heading_format__op_selected.php'); ?>

    <!-- multiple selections permission -->
    <div class="iwptp-editor-row-option">
        <label>
            <input type="checkbox" iwptp-model-key="redirect_enabled" />
            <?php esc_html_e('Enable category redirect', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
    </div>

    <!-- display all categories -->
    <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="redirect_enabled" iwptp-condition-val="true">
        <label>
            <input type="checkbox" iwptp-model-key="display_all" />
            <?php esc_html_e('Always display all categories', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
    </div>

    <!-- "Show all" label -->
    <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="single" iwptp-condition-val="true">
        <label>
            <?php esc_html_e('"Show All" option label', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
        <div iwptp-model-key="show_all_label" iwptp-block-editor iwptp-be-add-row="0"></div>
    </div>

    <!-- relabel -->
    <div class="iwptp-editor-row-option iwptp-toggle-options iwptp-row-accordion">

        <span class="iwptp-toggle-label">
            <?php esc_html_e('Custom term labels', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?>
        </span>

        <div class="iwptp-editor-loading" data-loading="terms" style="display: none;">
            <?php echo wp_kses(iwptp_icon('loader', 'iwptp-rotate'), iwptp_allowed_html_tags()); ?> <?php esc_html_e('Loading ...', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </div>

        <div class="iwptp-editor-row-option" iwptp-model-key="relabels">
            <div class="iwptp-editor-row iwptp-editor-custom-label-setup" iwptp-controller="relabels" iwptp-model-key="[]" iwptp-model-key-index="0" iwptp-row-template="relabel_rule_term_filter_element_2">
                <div class="iwptp-tabs">

                    <!-- triggers -->
                    <div class="iwptp-tab-triggers">
                        <div class="iwptp-tab-trigger" iwptp-content-template="term">
                            <?php esc_html_e('Term name', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                        </div>
                        <div class="iwptp-tab-trigger" iwptp-can-disable>
                            <?php esc_html_e('Clear label', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                        </div>
                    </div>

                    <!-- content: term label -->
                    <div class="iwptp-tab-content">
                        <div class="iwptp-editor-row-option">
                            <div iwptp-model-key="label" class="iwptp-term-relabel-editor" iwptp-block-editor="" iwptp-be-add-row="0"></div>
                        </div>
                    </div>

                    <!-- content: clear filter label -->
                    <div class="iwptp-tab-content">
                        <div class="iwptp-editor-row-option">
                            <input type="text" iwptp-model-key="clear_label" placeholder="[filter] : [option]">
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- exclude terms -->
    <div class="iwptp-editor-row-option">
        <label>
            <?php esc_html_e('Exclude category by slug', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            <small><?php esc_html_e('Enter one category slug per line', 'ithemeland-woocommerce-product-table-pro-lite'); ?></small>
        </label>
        <textarea iwptp-model-key="exclude_terms"></textarea>
    </div>

    <!-- hide empty -->
    <div class="iwptp-editor-row-option">
        <label>
            <input type="checkbox" iwptp-model-key="hide_empty"> <?php esc_html_e('Hide empty terms (not attached to any product on the site)', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
    </div>

    <!-- accordion always open -->
    <div class="iwptp-editor-row-option">
        <label>
            <input type="checkbox" iwptp-model-key="accordion_always_open"> <?php esc_html_e('Keep filter open by default if it is in sidebar', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
    </div>

    <!-- pre-open depth -->
    <div class="iwptp-editor-row-option">
        <label>
            <?php esc_html_e('Pre-open sub accordions till depth', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
        <input type="number" iwptp-model-key="pre_open_depth" min="0">
    </div>

    <!-- enable search -->
    <div class="iwptp-editor-row-option">
        <?php iwptp_checkbox('true', 'Enable search box for the filter options', 'search_enabled'); ?>
    </div>

    <!-- search placeholder -->
    <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="search_enabled" iwptp-condition-val="true">
        <label><?php esc_html_e('Placeholder for the search input box', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="search_placeholder">
    </div>
</div>

<div class="iwptp-element-settings-content-item" data-content="style">
    <?php include('style/filter.php'); ?>
</div>