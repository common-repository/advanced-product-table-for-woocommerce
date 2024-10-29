<?php
if (!empty($presets)) :
    foreach ($presets as $preset) :
        $slug = str_replace(' ', '-', strtolower($preset['name']));
?>
        <div class="iwptp-style-preset-item">
            <div class="iwptp-style-preset-item-image">
                <img src="<?php echo esc_url($preset['image_url']); ?>" alt="<?php echo esc_attr($preset['name']); ?>">
            </div>
            <strong><?php echo esc_html($preset['name']); ?></strong>
            <?php if (!empty($preset['data'])) : ?>
                <?php if (isset($preset['deletable']) && $preset['deletable']) : ?>
                    <button type="button" class="iwptp-button iwptp-button-red iwptp-style-preset-delete-button" title="Delete" value="<?php echo esc_attr($slug); ?>"><i class="dashicons dashicons-trash"></i></button>
                <?php endif; ?>
                <button type="button" class="iwptp-button iwptp-button-blue iwptp-style-preset-import-button" title="Import" value="<?php echo esc_attr($slug); ?>"><?php esc_html_e('Import', 'ithemeland-woocommerce-product-table-pro-lite'); ?></button>
                <textarea class="iwptp-hide iwptp-style-preset-item-data"><?php echo esc_html(trim(wp_json_encode($preset['data']))); ?></textarea>
            <?php else : ?>
                <div class="iwptp-presets-pro-badge">
                    <span><?php esc_html_e('Premium Version', 'ithemeland-woocommerce-product-table-pro-lite'); ?></span>
                </div>
            <?php endif; ?>
        </div>
<?php
    endforeach;
endif
?>