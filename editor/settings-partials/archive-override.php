<?php
$iwptp_post_query = new WP_Query(array(
    'post_type' => 'iwptp_product_table',
    'posts_per_page' => -1,
));

ob_start();
echo '<option value="">*None* &mdash; use default WC grid</option>';
echo '<option value="custom">*Custom shortcode* &mdash; manually enter shortcode with attributes</option>';
if ($iwptp_post_query->have_posts()) {
    while ($iwptp_post_query->have_posts()) {
        $iwptp_post_query->the_post();
        $title = get_the_title() ? get_the_title() : '*No name*';
        echo '<option value="' . esc_attr(get_the_ID()) . '">' . esc_html($title) . '</option>';
    }
}
$iwptp_table_options = ob_get_clean();

function iwptp_custom_shortcode_textarea($condition_prop, $condition_val, $model_key)
{
?>
    <div iwptp-panel-condition="prop" iwptp-condition-prop="<?php echo esc_attr($condition_prop); ?>" iwptp-condition-val="<?php echo esc_attr($condition_val); ?>">
        <textarea class='iwptp-editor-custom-table-shortcode' iwptp-model-key='<?php echo esc_attr($model_key); ?>' placeholder="Enter your [it_product_table] shortcode here..."></textarea>
    </div>
<?php
}
?>
<div class="iwptp-toggle-options" iwptp-model-key="archive_override">

    <div class="iwptp-editor-light-heading iwptp-toggle-label">
        Archive override
        <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?>
    </div>

    <div class="">

        <div class="iwptp-notice">Please read documentation how to enable this feature for your theme.</div>

        <!-- default -->
        <div class="iwptp-editor-row-option">
            <label>Default override table</label>
            <select iwptp-model-key='default'>
                <?php echo esc_html($iwptp_table_options); ?>
            </select>
            <?php iwptp_custom_shortcode_textarea('default', 'custom', 'default_custom') ?>
        </div>

        <?php
        // add the default option to table options
        $iwptp_table_options = '<option value="default">Default override table</option>' . esc_html($iwptp_table_options);
        ?>

        <!-- shop -->
        <div class="iwptp-editor-row-option">
            <label>Shop override table</label>
            <select iwptp-model-key='shop'>
                <?php echo esc_html($iwptp_table_options); ?>
            </select>
            <?php iwptp_custom_shortcode_textarea('shop', 'custom', 'shop_custom') ?>
        </div>

        <!-- search -->
        <div class="iwptp-editor-row-option">
            <label>Search override table <span class="iwptp-pro-badge" title="For Pro Version">Pro</span></label>
            <select iwptp-model-key='' disabled>
                <?php echo esc_html($iwptp_table_options); ?>
            </select>
        </div>

        <!-- category -->
        <div class="iwptp-toggle-options iwptp-editor-row-option" iwptp-model-key='' style="padding-left: 40px;">
            <div class="iwptp-editor-light-heading iwptp-toggle-label">Category override <span class="iwptp-pro-badge" title="For Pro Version">Pro</span> <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?></div>

            <div class="iwptp-editor-row-option">
                <label>Default category override table</label>
                <select iwptp-model-key='' disabled>
                    <?php echo esc_html($iwptp_table_options); ?>
                </select>
            </div>

            <div class="iwptp-editor-row-option" iwptp-controller="" iwptp-model-key="">
                <button class="iwptp-button iwptp-button-gray" disabled>
                    Add a rule
                </button>
            </div>
        </div>

        <!-- attribute -->
        <div class="iwptp-toggle-options iwptp-editor-row-option" iwptp-model-key='attribute' style="padding-left: 40px;">
            <div class="iwptp-editor-light-heading iwptp-toggle-label">Attribute override <span class="iwptp-pro-badge" title="For Pro Version">Pro</span> <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?></div>

            <div class="iwptp-editor-row-option">
                <label>Default attribute override table</label>
                <select iwptp-model-key='' disabled>
                    <?php echo esc_html($iwptp_table_options); ?>
                </select>
            </div>

            <!-- additional attribute overides -->
            <div class="iwptp-editor-row-option" iwptp-controller="archive_override_rows" iwptp-model-key="other_rules">
                <button class="iwptp-button iwptp-button-gray" disabled>
                    Add a rule
                </button>
            </div>
        </div>

        <!-- tag -->
        <div class="iwptp-toggle-options iwptp-editor-row-option" iwptp-model-key='tag' style="padding-left: 40px;">
            <div class="iwptp-editor-light-heading iwptp-toggle-label">Tag override <span class="iwptp-pro-badge" title="For Pro Version">Pro</span> <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?></div>

            <div class="iwptp-editor-row-option">
                <label>Default tag override table</label>
                <select iwptp-model-key="" disabled>
                    <?php echo esc_html($iwptp_table_options); ?>
                </select>
            </div>

            <div class="iwptp-editor-row-option" iwptp-controller="archive_override_rows" iwptp-model-key="other_rules">
                <button class="iwptp-button iwptp-button-gray" disabled>
                    Add a rule
                </button>
            </div>
        </div>
    </div>
</div>