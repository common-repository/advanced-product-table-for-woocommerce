<div class="iwptp-modal" id="iwptp-modal-live-preview" data-table-id="<?php echo (!empty($_GET['post_id'])) ? intval($_GET['post_id']) : ''; ?>">
    <div class="iwptp-modal-container">
        <div class="iwptp-modal-box iwptp-modal-box-lg">
            <div class="iwptp-admin-modal-content">
                <div class="iwptp-modal-title">
                    <h2><?php esc_html_e('Live preview', 'ithemeland-woocommerce-product-table-pro-lite'); ?></h2>
                    <ul class="iwptp-modal-live-preview-devices">
                        <li><a href="#" class="iwptp-modal-live-preview-device selected" data-device="laptop"><?php esc_html_e('Laptop', 'ithemeland-woocommerce-product-table-pro-lite'); ?></a></li>
                        <li><a href="#" class="iwptp-modal-live-preview-device" data-device="tablet"><?php esc_html_e('Tablet', 'ithemeland-woocommerce-product-table-pro-lite'); ?></a></li>
                        <li><a href="#" class="iwptp-modal-live-preview-device" data-device="phone"><?php esc_html_e('Phone', 'ithemeland-woocommerce-product-table-pro-lite'); ?></a></li>
                    </ul>
                    <button type="button" class="iwptp-modal-close" data-toggle="modal-close">
                        <i class="lni lni-close"></i>
                    </button>
                </div>
                <div class="iwptp-modal-body">
                    <div class="iwptp-wrap">
                        <div class="iwptp-modal-loading">
                            <img src="<?php echo esc_url(IWPTPL_IMAGES_URL . 'loading.gif'); ?>" width="50" height="50">
                        </div>
                        <div id="iwptp-modal-live-preview-container"></div>
                    </div>
                </div>
                <div class="iwptp-modal-footer">
                    <button type="button" class="iwptp-button iwptp-button-blue" style="float: right;" data-toggle="modal-close">
                        <?php esc_html_e('Close', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>