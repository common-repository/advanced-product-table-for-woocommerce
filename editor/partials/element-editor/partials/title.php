<div class="iwptp-element-settings-content-item active" data-content="general">
    <div class="iwptp-editor-row-option">
        <label>
            <input type="checkbox" iwptp-model-key="product_link_enabled" />
            <?php esc_html_e('Link title to the product\'s page', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
    </div>

    <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="product_link_enabled" iwptp-condition-val="true">
        <label>
            <input type="checkbox" iwptp-model-key="target_new_page" />
            <?php esc_html_e('Open the product link on a new page', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
    </div>

    <!-- HTML tag -->
    <div class="iwptp-editor-row-option">
        <label><?php esc_html_e('HTML tag', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <div class="">
            <select iwptp-model-key="html_tag">
                <?php
                $options = array(
                    'span' => 'span',
                    'h1'  => 'H1',
                    'h2'  => 'H2',
                    'h3'  => 'H3',
                    'h4'  => 'H4',
                );
                foreach ($options as $val => $label) {
                    echo '<option value="' . esc_attr($val) . '">' . esc_html($label) . '</option>';
                }
                ?>
            </select>
            <!-- <label>
      <small>
        <?php echo esc_html("<span> wrapper won't be applied over <a> tag"); ?>
      </small>
    </label> -->
        </div>
    </div>

    <?php include('html-class.php'); ?>

    <?php include('condition/outer.php'); ?>
</div>

<!-- style -->
<div class="iwptp-element-settings-content-item" data-content="style" iwptp-model-key="style">
    <?php include('style/common.php'); ?>
</div>