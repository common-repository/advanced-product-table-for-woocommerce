<div class="iwptp-element-settings-content-item active" data-content="general">
    <!-- term separator -->
    <div class="iwptp-editor-row-option">
        <label><?php esc_html_e('Separator between multiple terms', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <div iwptp-model-key="separator" class="iwptp-separator-editor" iwptp-block-editor="" iwptp-be-add-row="0"></div>
    </div>

    <!-- exclude terms -->
    <div class="iwptp-editor-row-option">
        <label>
            <?php esc_html_e('Exclude category by slug', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            <small><?php esc_html_e('Enter one category slug per line', 'ithemeland-woocommerce-product-table-pro-lite'); ?></small>
        </label>
        <textarea iwptp-model-key="exclude_terms"></textarea>
    </div>

    <!-- link term to filter -->
    <div class="iwptp-editor-row-option">
        <label>
            <?php esc_html_e('Action on click', 'ithemeland-woocommerce-product-table-pro-lite'); ?>:
        </label>
        <?php iwptp_radio('', __('Do nothing', 'ithemeland-woocommerce-product-table-pro-lite'), 'click_action'); ?>
        <?php iwptp_radio('archive_redirect', __('Go to archive page', 'ithemeland-woocommerce-product-table-pro-lite'), 'click_action'); ?>
        <?php iwptp_radio('trigger_filter', __('Trigger matching filter in table navigation', 'ithemeland-woocommerce-product-table-pro-lite'), 'click_action'); ?>
    </div>

    <!-- relabel -->
    <div class="iwptp-editor-row-option iwptp-toggle-options iwptp-row-accordion">

        <span class="iwptp-toggle-label">
            <?php esc_html_e('Custom category labels', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?>
        </span>

        <div class="iwptp-editor-loading" data-loading="terms" style="display: none;">
            <?php echo wp_kses(iwptp_icon('loader', 'iwptp-rotate'), iwptp_allowed_html_tags()); ?> <?php esc_html_e('Loading ...', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </div>

        <div class="iwptp-editor-row-option" iwptp-model-key="relabels">
            <div class="iwptp-editor-custom-label-setup" iwptp-controller="relabels" iwptp-model-key="[]" iwptp-model-key-index="0" iwptp-row-template="relabel_rule_term_column_element_2">

                <div class="iwptp-tabs">

                    <!-- triggers -->
                    <div class="iwptp-tab-triggers">
                        <div class="iwptp-tab-trigger" iwptp-content-template="term">
                            <?php esc_html_e('Term name', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                        </div>
                        <div class="iwptp-tab-trigger">
                            <?php esc_html_e('Style', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                        </div>
                    </div>

                    <!-- content: term label -->
                    <div class="iwptp-tab-content">
                        <div class="iwptp-editor-row-option">
                            <div iwptp-model-key="label" class="iwptp-term-relabel-editor" iwptp-block-editor="" iwptp-be-add-row="0" iwptp-be-add-element-partial="add-term-element-col"></div>
                        </div>
                    </div>

                    <!-- content: term style -->
                    <div class="iwptp-tab-content">

                        <div class="iwptp-editor-row-option" iwptp-model-key="style">
                            <div class="iwptp-editor-row-option" iwptp-model-key="[id]">

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

                                <!-- border color -->
                                <div class="iwptp-editor-row-option">
                                    <label><?php esc_html_e('Border color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                                    <input type="text" iwptp-model-key="border-color" class="iwptp-color-picker">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('condition/outer.php'); ?>
</div>

<div class="iwptp-element-settings-content-item" data-content="style">
    <?php include('style/parent-child.php'); ?>
</div>