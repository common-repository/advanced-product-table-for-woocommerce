<?php
// locate and include partials - nav + cell template + heading content

$partials = array_diff(scandir(__DIR__ . '/partials'), array('..', '.', '.DS_Store'));
foreach ($partials as $partial) :
    if (substr($partial, -4) == '.php') :
?>
        <script type="text/template" data-iwptp-partial="<?php echo esc_attr(substr($partial, 0, -4)); ?>">
            <?php
            if ('add' != substr($partial, 0, 3) || 'add_selected_to_cart.php' == $partial) :
                $x1 = explode('__', substr($partial, 0, -4));
                $elm_name = ucwords(implode(' ', explode('_', $x1[0])));

                switch ($elm_name) {
                    case 'Apply Reset':
                        $elm_name = esc_html__('Apply / Reset', 'ithemeland-woocommerce-product-table-pro-lite');
                        break;

                    case 'Html':
                        $elm_name = esc_html__('HTML', 'ithemeland-woocommerce-product-table-pro-lite');
                        break;

                    case 'Download Csv':
                        $elm_name = esc_html__('Download CSV', 'ithemeland-woocommerce-product-table-pro-lite');
                        break;
                }
            ?>

                <h2 class="iwptp-element-settings-title">
                    <a href="javascript:;" class="iwptp-block-editor-lightbox-back" title="Back">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                    </a>
                    <span>Edit element: <?php echo esc_html($elm_name); ?></span>
                </h2>

                <?php
                include('partials/element-setting-header.php');
            endif;
            include('partials/' . $partial);
                ?>
        </script>
<?php
    endif;
endforeach;
