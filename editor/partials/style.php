<!-- Presets -->
<div class="iwptp-editor-option-row" id="iwptp-style-presets">
    <h2 class="iwptp-editor-light-heading">
        <?php esc_html_e('Presets', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
    </h2>
    <div class="iwptp-style-preset-items">
        <?php include "style-presets.php"; ?>
    </div>
</div>

<?php
foreach (array('laptop', 'tablet', 'phone') as $device) {
?>

    <!-- <?php echo esc_html($device) . ' style'; ?> -->
    <div class="iwptp-device-style" id="iwptp-style-<?php echo esc_attr($device); ?>" data-iwptp-device="<?php echo esc_attr($device); ?>" iwptp-model-key="<?php echo esc_attr($device); ?>">
        <h2 class="iwptp-editor-light-heading">
            <?php
            echo esc_html(ucfirst($device) . ' Style');
            // inheritance option
            if (in_array($device, array('phone', 'tablet'))) {
                $label = "Inherit " . ($device == 'tablet' ? 'Laptop' : 'Tablet') . " Style";
                $model_key =  str_replace(' ', '_', strtolower($label));
            ?>
                <div class="iwptp-inheritance-option">
                    <label>
                        <input type="checkbox" iwptp-model-key="<?php echo esc_attr($model_key); ?>">
                        <?php echo esc_html($label); ?>
                    </label>
                </div>
            <?php
            }
            ?>
        </h2>
        <?php
        foreach (array(
            'container' => '',
            'headings' => '',
            'cells' => '[container] td.iwptp-cell',
            'odd_rows' => '[container] tr.iwptp-odd td',
            'even_rows' => '[container] tr.iwptp-even td',
            'borders' => '',
            'other' => '',
        ) as $elm => $selector) {
        ?>
            <!-- <?php echo esc_html($elm); ?> -->
            <div class="iwptp-editor-option-row iwptp-toggle-options iwptp-<?php echo esc_attr($elm); ?>-style" <?php if ($selector) echo 'iwptp-model-key="' . esc_attr($selector) . '"' ?>>
                <span class="iwptp-toggle-label">
                    <?php
                    echo wp_kses(str_replace('_', ' ', ucfirst($elm)), iwptp_allowed_html_tags());
                    if (!in_array($elm, ['headings', 'container'])) {
                        echo ' <span class="iwptp-pro-badge" title="For Pro Version">Pro</span>';
                    }
                    echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags());
                    ?>
                </span>

                <?php require(__DIR__ . '/style/' . $elm . '.php'); ?>
            </div>
        <?php
        }
        ?>
    </div>

<?php

}
?>

<!-- Navigation style -->
<div class="iwptp-device-style" iwptp-model-key="navigation" id="iwptp-style-navigation">
    <h2 class="iwptp-editor-light-heading">
        <?php esc_html_e('Navigation style', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
    </h2>
    <?php
    foreach (array(
        'sidebar' => '', // split into lower level selectors
        'header' => '', // ""
        'footer' => '', // ""
        'modal' => '', // ""
        'pagination' => ''  // ""
    ) as $elm => $selector) {
    ?>
        <!-- <?php echo esc_html($elm); ?> -->
        <div class="iwptp-editor-option-row iwptp-toggle-options iwptp-<?php echo esc_attr($elm); ?>-style" <?php if ($selector) echo 'iwptp-model-key="' . esc_attr($selector) . '"' ?>>
            <span class="iwptp-toggle-label">
                <?php
                echo wp_kses(str_replace('_', ' ', ucfirst($elm)), iwptp_allowed_html_tags());
                if (!in_array($elm, ['header', 'footer'])) {
                    echo ' <span class="iwptp-pro-badge" title="For Pro Version">Pro</span>';
                }
                echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags());
                ?>
            </span>

            <?php require(__DIR__ . '/style/' . $elm . '.php'); ?>
        </div>
    <?php
    }
    ?>
</div>

<!-- Mini cart style -->
<div class="iwptp-device-style" iwptp-model-key="mini_cart" id="iwptp-style-mini-cart">
    <h2 class="iwptp-editor-light-heading">
        <?php esc_html_e('Mini cart style', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
    </h2>
    <?php
    foreach ([
        'general' => __('General', 'ithemeland-woocommerce-product-table-pro-lite'),
        'button' => __('Button', 'ithemeland-woocommerce-product-table-pro-lite') . ' <span class="iwptp-pro-badge" title="For Pro Version">Pro</span>',
        'area' => __('Area', 'ithemeland-woocommerce-product-table-pro-lite'),
    ] as $elm => $label) {
    ?>
        <!-- <?php echo esc_html($elm); ?> -->
        <div class="iwptp-editor-option-row iwptp-toggle-options iwptp-<?php echo esc_attr($elm); ?>-style">
            <span class="iwptp-toggle-label">
                <?php echo wp_kses($label, iwptp_allowed_html_tags()); ?>
                <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?>
            </span>

            <?php require(__DIR__ . '/style/mini-cart/' . $elm . '.php'); ?>
        </div>
    <?php
    }
    ?>
</div>

<!-- html class -->
<div class="iwptp-editor-option-row" style="padding: 20px 0; border-bottom: 1px solid #e4e4e4;">
    <label><?php esc_html_e('HTML Class', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
    <input type="text" class="iwptp-style-html-class" iwptp-model-key="html_class">
</div>

<!-- CSS -->
<div class="iwptp-editor-option-row" style="padding-top: 5px;">
    <label>
        <?php esc_html_e('CSS', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        <span class="iwptp-selectors iwptp-toggle iwptp-toggle-off">
            <span class="iwptp-toggle-trigger iwptp-noselect">
                <?php echo wp_kses(iwptp_icon('chevron-down', 'iwptp-toggle-is-off'), iwptp_allowed_html_tags()); ?>
                <?php echo wp_kses(iwptp_icon('chevron-up', 'iwptp-toggle-is-on'), iwptp_allowed_html_tags()); ?>
                <?php esc_html_e('Show selectors', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            </span>
            <span class="iwptp-toggle-tray">

                <?php echo wp_kses(iwptp_icon('x', 'iwptp-toggle-x'), iwptp_allowed_html_tags()); ?>

                <table>
                    <thead>
                        <tr>
                            <td><strong>Selector</strong></td>
                            <td><strong>Description</strong></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>[container]</td>
                            <td>Target the entire container with table and navigation elements (filters, pagination)</td>
                        </tr>
                        <tr>
                            <td>[id]</td>
                            <td>Placeholder for the table post id</td>
                        </tr>
                        <tr>
                            <td>[table]</td>
                            <td>Target the table</td>
                        </tr>
                        <tr>
                            <td>[heading_row]</td>
                            <td>Target the heading row</td>
                        </tr>
                        <tr>
                            <td>[heading_cell]</td>
                            <td>Target the heading cells</td>
                        </tr>
                        <tr>
                            <td>[heading_cell_even]</td>
                            <td>Target even heading cells</td>
                        </tr>
                        <tr>
                            <td>[heading_cell_odd]</td>
                            <td>Target odd heading cells</td>
                        </tr>
                        <tr>
                            <td>[row]</td>
                            <td>Target the table row element</td>
                        </tr>
                        <tr>
                            <td>[row_even]</td>
                            <td>Target even rows</td>
                        </tr>
                        <tr>
                            <td>[row_odd]</td>
                            <td>Target odd rows</td>
                        </tr>
                        <tr>
                            <td>[cell]</td>
                            <td>Target all the table cells</td>
                        </tr>
                        <tr>
                            <td>[cell_even]</td>
                            <td>Target even column cells</td>
                        </tr>
                        <tr>
                            <td>[cell_odd]</td>
                            <td>Target odd column cells</td>
                        </tr>
                        <tr>
                            <td>[tablet] ... [/tablet]</td>
                            <td>Replace ... with the css code meant only for tablet size devices</td>
                        </tr>
                        <tr>
                            <td>[phone] ... [/phone]</td>
                            <td>Replace ... with the css code meant only for phone size devices</td>
                        </tr>
                    </tbody>
                </table>

            </span>
        </span>
    </label>
    <textarea class="iwptp-style" id="iwptp-css" iwptp-model-key="css" placeholder="<?php esc_html_e("Enter custom CSS here...", "ithemeland-woocommerce-product-table-pro-lite"); ?>"></textarea>
</div>

<!-- save as preset -->
<div class="iwptp-editor-option-row iwptp-style-save-as-preset">
    <span class="iwptp-pro-badge" title="For Pro Version">Pro</span>
    <input type="text" placeholder="Preset name ... " disabled>
    <div class="iwptp-style-save-as-preset-buttons">
        <button type="button" class="iwptp-button iwptp-button-gray" style="float: right;" disabled><?php esc_html_e('Save as new preset', 'ithemeland-woocommerce-product-table-pro-lite'); ?></button>
        <button type="button" class="iwptp-button iwptp-button-gray" style="float: right;" disabled><?php esc_html_e('Choose image', 'ithemeland-woocommerce-product-table-pro-lite'); ?></button>
    </div>
</div>