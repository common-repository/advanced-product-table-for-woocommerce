<div class="iwptp-element-settings-content-item active" data-content="general">
    <div class="iwptp-editor-row-option">
        <label><?php esc_html_e('Type', 'ithemeland-woocommerce-product-table-pro'); ?></label>
        <select iwptp-model-key="pagination_type">
            <option value="number"><?php esc_html_e('Number', 'ithemeland-woocommerce-product-table-pro'); ?></option>
            <option value="next_prev"><?php esc_html_e('Next / Prev', 'ithemeland-woocommerce-product-table-pro'); ?></option>
        </select>
    </div>

    <div class="iwptp-editor-row-option">
        <label> <?php esc_html_e('Next Text', 'ithemeland-woocommerce-product-table-pro'); ?> <br>
            <?php esc_html_e('For example:', 'ithemeland-woocommerce-product-table-pro'); ?> <br>
            en_US: Next
        </label>
        <textarea iwptp-model-key="next_text"></textarea>
    </div>

    <div class="iwptp-editor-row-option">
        <label> <?php esc_html_e('Prev Text', 'ithemeland-woocommerce-product-table-pro'); ?> <br>
            <?php esc_html_e('For example:', 'ithemeland-woocommerce-product-table-pro'); ?> <br>
            en_US: Prev
        </label>
        <textarea iwptp-model-key="prev_text"></textarea>
    </div>
</div>

<div class="iwptp-element-settings-content-item" data-content="style" iwptp-model-key="style">
    <div iwptp-model-key=".iwptp-pagination">
        <div class="iwptp-editor-option-row">
            <label><?php esc_html_e('Align', 'ithemeland-woocommerce-product-table-pro'); ?></label>
            <select iwptp-model-key="text-align">
                <option value="left"><?php esc_html_e('Left', 'ithemeland-woocommerce-product-table-pro'); ?></option>
                <option value="right"><?php esc_html_e('Right', 'ithemeland-woocommerce-product-table-pro'); ?></option>
                <option value="center"><?php esc_html_e('Center', 'ithemeland-woocommerce-product-table-pro'); ?></option>
            </select>
        </div>
    </div>
</div>