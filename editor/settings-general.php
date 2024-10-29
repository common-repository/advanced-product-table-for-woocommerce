<?php
include('settings-partials/archive-override.php');
include('settings-partials/search.php');
do_action('iwptp_settings_panel_end');
?>

<!-- save data -->
<div class="iwptp-reset-global-settings-container">
    <a class="iwptp-reset-global-settings" href="<?php echo esc_url(admin_url('edit.php?post_type=iwptp_product_table&page=iwptp-settings&iwptp_reset_global_settings=true')); ?>">Reset settings</a>
</div>
<div class="iwptp-editor-save-table-clear"></div>
<div class="iwptp-editor-save-table" style="margin-top: 30px">
    <form class="iwptp-save-data" action="iwptp_save_global_settings" method="post">
        <input name="nonce" type="hidden" value="<?php echo esc_attr(wp_create_nonce("iwptp")); ?>">
        <button type="submit" class="iwptp-editor-save-button iwptp-button iwptp-button-lg iwptp-button-blue"><?php esc_html_e("Save settings", "ithemeland-woocommerce-product-table-pro-lite"); ?></button>
    </form>
</div>