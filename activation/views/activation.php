<?php require_once IWPTPL_PLUGIN_PATH . "editor/header.php"; ?>
<div id="iwptp-body">
    <div class="iwptp-dashboard-body">
        <div id="iwptp-activation">
            <?php if (isset($is_active) && $is_active === true && $activation_skipped !== true) : ?>
                <div class="iwptp-wrap">
                    <div class="iwptp-tab-middle-content">
                        <div id="iwptp-activation-info">
                            <strong><?php esc_html_e("Congratulations, Your plugin is activated successfully. Let's Go!", 'ithemeland-woocommerce-product-table-pro-lite') ?></strong>
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <div class="iwptp-wrap iwptp-activation-form">
                    <div class="iwptp-tab-middle-content">
                        <?php if (!empty($flush_message) && is_array($flush_message)) : ?>
                            <div class="iwptp-alert <?php echo ($flush_message['message'] == "Success !") ? "iwptp-alert-success" : "iwptp-alert-danger"; ?>">
                                <span><?php echo esc_html($flush_message['message']); ?></span>
                            </div>
                        <?php endif; ?>
                        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" id="iwptp-activation-form">
                            <?php wp_nonce_field('iwptp_post_nonce'); ?>
                            <h3 class="iwptp-activation-top-alert">Fill the below form to get the latest updates' news and <strong style="text-decoration: underline;">Special Offers(Discount)</strong>, Otherwise, Skip it!</h3>
                            <input type="hidden" name="action" value="iwptp_activation_plugin">
                            <div class="iwptp-activation-field">
                                <label for="iwptp-activation-email"><?php esc_html_e('Email', 'ithemeland-woocommerce-product-table-pro-lite'); ?> </label>
                                <input type="email" name="email" placeholder="Email ..." id="iwptp-activation-email">
                            </div>
                            <div class="iwptp-activation-field">
                                <label for="iwptp-activation-industry"><?php esc_html_e('What is your industry?', 'ithemeland-woocommerce-product-table-pro-lite'); ?> </label>
                                <select name="industry" id="iwptp-activation-industry">
                                    <option value=""><?php esc_html_e('Select', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                                    <?php
                                    if (!empty($industries)) :
                                        foreach ($industries as $industry_key => $industry_label) :
                                    ?>
                                            <option value="<?php echo esc_attr($industry_key); ?>"><?php echo esc_html($industry_label); ?></option>
                                    <?php
                                        endforeach;
                                    endif
                                    ?>
                                </select>
                            </div>
                            <input type="hidden" name="activation_type" id="iwptp-activation-type" value="">
                            <button type="button" id="iwptp-activation-activate" class="iwptp-button iwptp-button-lg iwptp-button-blue" value="1"><?php esc_html_e('Activate', 'ithemeland-woocommerce-product-table-pro-lite'); ?></button>
                            <button type="button" id="iwptp-activation-skip" class="iwptp-button iwptp-button-lg iwptp-button-gray" style="float: left;" value="skip"><?php esc_html_e('Skip', 'ithemeland-woocommerce-product-table-pro-lite'); ?></button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require_once IWPTPL_PLUGIN_PATH . "editor/footer.php"; ?>