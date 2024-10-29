<?php

if (!class_exists('WooCommerce')) {
    return;
}

wp_enqueue_style('dashicons');

wp_enqueue_style('iwptp-sweetalert',  plugin_dir_url(__FILE__) . 'assets/sweetalert.css', false, IWPTPL_VERSION);

// antiscroll
wp_enqueue_script('antiscroll',  plugin_dir_url(__FILE__) . 'assets/antiscroll/js.js', 'jquery', IWPTPL_VERSION, true);
wp_enqueue_style('antiscroll',  plugin_dir_url(__FILE__) . 'assets/antiscroll/css.css', false, IWPTPL_VERSION);

// freeze table
wp_enqueue_script('freeze_table',  plugin_dir_url(__FILE__) . 'assets/freeze_table/js.js', array('jquery', 'antiscroll'), IWPTPL_VERSION, true);
include(IWPTPL_PLUGIN_PATH . 'assets/freeze_table/tpl.html');
wp_enqueue_style('freeze_table',  plugin_dir_url(__FILE__) . 'assets/freeze_table/css.css', false, IWPTPL_VERSION);

// photoswipe
wp_enqueue_script('photoswipe', plugin_dir_url(WC_PLUGIN_FILE) . 'assets/js/photoswipe/photoswipe.min.js', false, IWPTPL_VERSION, true);
wp_enqueue_script('photoswipe-ui-default', plugin_dir_url(WC_PLUGIN_FILE) . 'assets/js/photoswipe/photoswipe-ui-default.min.js', array('photoswipe'), IWPTPL_VERSION, true);
wp_enqueue_style('photoswipe', plugin_dir_url(WC_PLUGIN_FILE) . 'assets/css/photoswipe/photoswipe.min.css', false, IWPTPL_VERSION);
wp_enqueue_style('photoswipe-default-skin', plugin_dir_url(WC_PLUGIN_FILE) . 'assets/css/photoswipe/default-skin/default-skin.min.css', false, IWPTPL_VERSION);
add_action('wp_footer', 'iwptp_woocommerce_photoswipe');

// multirange
wp_enqueue_script('multirange',  plugin_dir_url(__FILE__) . 'assets/multirange/js.js', 'jquery', IWPTPL_VERSION, true);
wp_enqueue_style('multirange',  plugin_dir_url(__FILE__) . 'assets/multirange/css.css', false, IWPTPL_VERSION);

// WC measurement price calculator -- script
if (class_exists('WC_Measurement_Price_Calculator')) {
    // custom script
    wp_enqueue_script('iwptp-wc-price-calculator', IWPTPL_PLUGIN_URL . 'assets/js/wc-measurement-price-calculator.js', array('jquery'), IWPTPL_VERSION, true);
    // tooltip required by MPC
    wp_enqueue_script('jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip.min.js', array('jquery'), IWPTPL_VERSION, true);
}

// Name your price -- script
if (function_exists('WC_Name_Your_Price')) {
    wp_enqueue_script('woocommerce-nyp', WC_Name_Your_Price()->plugin_url() . '/assets/js/name-your-price.js', array('jquery', 'accounting'), WC_Name_Your_Price()->version, true);

    $wc_nyp_script_params = array(
        'currency_format_num_decimals' => wc_get_price_decimals(),
        'currency_format_symbol' => get_woocommerce_currency_symbol(),
        'currency_format_decimal_sep' => wc_get_price_decimal_separator(),
        'currency_format_thousand_sep' => wc_get_price_thousand_separator(),
        'currency_format' => str_replace(array('%1$s', '%2$s'), array('%s', '%v'), get_woocommerce_price_format()), // For accounting.js.
        'annual_price_factors' => WC_Name_Your_Price_Helpers::annual_price_factors(),
        'i18n_subscription_string' => __('%price / %period', 'wc_name_your_price'),
    );
    wp_localize_script('woocommerce-nyp', 'woocommerce_nyp_params', apply_filters('wc_nyp_script_params', $wc_nyp_script_params));

    $iwptp_nyp_error_message_templates = apply_filters(
        'wc_nyp_error_message_templates',
        array(
            'invalid-product' => __('This is not a valid product.', 'wc_name_your_price'),
            'invalid' => __('&quot;%%TITLE%%&quot; could not be added to the cart. Please enter a valid, positive number.', 'wc_name_your_price'),
            'minimum' => __('&quot;%%TITLE%%&quot; could not be added to the cart. Please enter at least %%MINIMUM%%.', 'wc_name_your_price'),
            'hide_minimum' => __('&quot;%%TITLE%%&quot; could not be added to the cart. Please enter a higher amount.', 'wc_name_your_price'),
            'minimum_js' => __('Please enter at least %%MINIMUM%%.', 'wc_name_your_price'),
            'hide_minimum_js' => __('Please enter a higher amount.', 'wc_name_your_price'),
            'maximum' => __('&quot;%%TITLE%%&quot; could not be added to the cart. Please enter less than or equal to %%MAXIMUM%%.', 'wc_name_your_price'),
            'maximum_js' => __('Please enter less than or equal to %%MAXIMUM%%.', 'wc_name_your_price'),
            'empty' => __('Please enter an amount.', 'wc_name_your_price'),
            'minimum-cart' => __('&quot;%%TITLE%%&quot; cannot be purchased. Please enter at least %%MINIMUM%%.', 'wc_name_your_price'),
            'maximum-cart' => __('&quot;%%TITLE%%&quot; cannot be purchased. Please enter less than or equal to %%MAXIMUM%%.', 'wc_name_your_price'),
        )
    );
    wp_localize_script('woocommerce-nyp', 'iwptp_nyp_error_message_templates', $iwptp_nyp_error_message_templates);
}

wp_enqueue_script('iwptp-sweetalert',  plugin_dir_url(__FILE__) . 'assets/sweetalert.min.js', array('jquery', 'freeze_table'), IWPTPL_VERSION, true);
wp_enqueue_script('iwptp',  plugin_dir_url(__FILE__) . 'assets/js.js', array('jquery', 'freeze_table'), IWPTPL_VERSION, true);
wp_localize_script('iwptp', 'iwptp_i18n', array(
    'i18n_no_matching_variations_text' => esc_attr__('Sorry, no products matched your selection. Please choose a different combination.', 'woocommerce'),
    'i18n_make_a_selection_text'       => esc_attr__('Please select some product options before adding this product to your cart.', 'woocommerce'),
    'i18n_unavailable_text'            => esc_attr__('Sorry, this product is unavailable. Please choose a different combination.', 'woocommerce'),
    'lang' => !empty($_REQUEST['lang']) ? sanitize_text_field($_REQUEST['lang']) : '',
));

wp_localize_script('iwptp', 'IWPTP_DATA', array(
    'ajax_url' => admin_url('admin-ajax.php'),
    'ajax_nonce' => wp_create_nonce('iwptp_ajax_nonce')
));

$settings = iwptp_get_settings_data();

if (empty($settings['cart_widget']) || empty($settings['cart_widget']['enabled_site_wide'])) {
    $settings['cart_widget'] = array(
        'enabled_site_wide' => false,
        'exclude_urls' => false,
        'include_urls' => false,
        'link' => 'cart',
    );
}

$responsive_checkbox_trigger = false;
if (!empty($settings['checkbox_trigger']) && !empty($settings['checkbox_trigger']['r_toggle']) && $settings['checkbox_trigger']['r_toggle'] === 'enabled') {
    $responsive_checkbox_trigger = true;
}

wp_localize_script('iwptp', 'iwptp_params', array(
    'ajax_url' => admin_url('admin-ajax.php'),
    'wc_ajax_url' => WC_AJAX::get_endpoint('%%endpoint%%'),
    'ajax_nonce' => wp_create_nonce('iwptp_ajax_nonce'),
    'shop_url' => get_permalink(wc_get_page_id('shop')),
    'cart_url' => wc_get_cart_url(),
    'checkout_url' => wc_get_checkout_url(),
    'shop_table_id' => iwptp_get_shop_table_id(),
    'site_url' => site_url(),
    'cart_widget_enabled_site_wide' => $settings['cart_widget']['enabled_site_wide'],
    'cart_widget_exclude_urls' => !empty($settings['cart_widget']['exclude_urls']) ? $settings['cart_widget']['exclude_urls'] : false,
    'cart_widget_include_urls' => !empty($settings['cart_widget']['include_urls']) ? $settings['cart_widget']['include_urls'] : false,
    'initially_empty_cart' => !WC()->cart || !WC()->cart->get_cart_contents_count(),
    'cart' => WC()->cart,
    'initial_device' => iwptp_get_device(),
    'breakpoints' => apply_filters('iwptp_breakpoints', $GLOBALS['iwptp_breakpoints']),
    'price_decimals' => wc_get_price_decimals(),
    'price_decimal_separator' => wc_get_price_decimal_separator(),
    'price_thousand_separator' => wc_get_price_thousand_separator(),
    'price_format' => get_woocommerce_price_format(),
    'currency_symbol' => get_woocommerce_currency_symbol(),
    'initial_device' => iwptp_get_device(),
    'responsive_checkbox_trigger' => $responsive_checkbox_trigger,
));

wp_enqueue_script('wc-add-to-cart', apply_filters('woocommerce_get_asset_url', plugins_url('assets/js/frontend/add-to-cart.js', WC_PLUGIN_FILE), 'assets/js/frontend/add-to-cart.js'), array('jquery', 'wp-util'), WC_VERSION);
wp_enqueue_script('wc-add-to-cart-variation', apply_filters('woocommerce_get_asset_url', plugins_url('assets/js/frontend/add-to-cart-variation.js', WC_PLUGIN_FILE), 'assets/js/frontend/add-to-cart-variation.js'), array('jquery', 'wp-util'), WC_VERSION);
wp_enqueue_script('wp-mediaelement');

include(IWPTPL_PLUGIN_PATH . 'templates/form-loading-screen.php');


// -- styles
wp_enqueue_style('iwptp',  plugin_dir_url(__FILE__) . 'assets/css.css', false, IWPTPL_VERSION);
wp_enqueue_style('wp-mediaelement');

// media player button hover fix
wp_add_inline_style('iwptp', '
.mejs-button>button {
  background: transparent url(' . includes_url() . 'js/mediaelement/mejs-controls.svg) !important;
}
.mejs-mute>button {
  background-position: -60px 0 !important;
}    
.mejs-unmute>button {
  background-position: -40px 0 !important;
}    
.mejs-pause>button {
  background-position: -20px 0 !important;
}    
');

// WC measurement price calculator -- style
if (class_exists('WC_Measurement_Price_Calculator')) {
    wp_add_inline_style(
        'iwptp',
        '.iwptp #price_calculator {
            width: auto;
        }
        
        .iwptp #price_calculator input[type="text"],
        .iwptp #price_calculator input[type="number"],
        .iwptp #price_calculator input[type="text"],
        .iwptp #price_calculator input[type="number"] {
            width: 100px;
        }
        
        #tiptip_holder {
            display: none;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 99999;
        }
    
        #tiptip_holder.tip_top {
            padding-bottom: 5px;
        }
    
        #tiptip_holder.tip_top #tiptip_arrow_inner {
            margin-top: -7px;
            margin-left: -6px;
            border-top-color: #464646;
        }
    
        #tiptip_holder.tip_bottom {
            padding-top: 5px;
        }
    
        #tiptip_holder.tip_bottom #tiptip_arrow_inner {
            margin-top: -5px;
            margin-left: -6px;
            border-bottom-color: #464646;
        }
    
        #tiptip_holder.tip_right {
            padding-left: 5px;
        }
    
        #tiptip_holder.tip_right #tiptip_arrow_inner {
            margin-top: -6px;
            margin-left: -5px;
            border-right-color: #464646;
        }
    
        #tiptip_holder.tip_left {
            padding-right: 5px;
        }
    
        #tiptip_holder.tip_left #tiptip_arrow_inner {
            margin-top: -6px;
            margin-left: -7px;
            border-left-color: #464646;
        }
    
        #tiptip_content, .chart-tooltip {
            font-size: 11px;
            color: #fff;
            padding: 0.5em 0.5em;
            background: #464646;
            -webkit-border-radius: 3px;
            -moz-border-radius: 3px;
            border-radius: 3px;
            -webkit-box-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
            -moz-box-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
            box-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 150px;
        }
    
        #tiptip_content code, .chart-tooltip code {
            background: #888;
            padding: 1px;
        }
    
        #tiptip_arrow, #tiptip_arrow_inner {
            position: absolute;
            border-color: transparent;
            border-style: solid;
            border-width: 6px;
            height: 0;
            width: 0;
        }            
        '
    );
}

// NP Quote Request WooCommerce -- style
if (class_exists('GPLS_WOO_RFQ')) {
    wp_add_inline_style(
        'iwptp',
        '.iwptp-cw-footer,
        .iwptp-cw-separator {
            display: none !important;
        }'
    );
}

// theme specific
$theme_slug = trim(get_option('stylesheet'));

if (substr($theme_slug, -6) == '-child') {
    $theme_slug = substr($theme_slug, 0, -6);
}

switch ($theme_slug) {
    case 'dt-the7':
        wp_add_inline_style(
            'iwptp',
            ' .woocommerce-variation-add-to-cart .minus,
            .woocommerce-variation-add-to-cart .plus {
                padding: 0 !important;
                height: 40px !important;
                width: 25px !important;
                text-align: center !important;
            }'
        );
        break;
    case 'jupiterx':
        wp_add_inline_style(
            'iwptp',
            '.iwptp-quantity .input-group {
                display: none !important;
            }
            
            .iwptp-quantity input.qty {
                display: inline-block !important;
            }'
        );
        break;
    case 'jupiter':
        wp_add_inline_style(
            'iwptp',
            '.iwptp-modal .cart select {
                height: 45px !important;
                font-size: 18px !important;
                line-height: 20px !important;
                font-weight: normal !important;
            }

            .iwptp-modal .cart input.qty {
                width: 80px !important;
                text-align: center !important;
                padding-right: 36px !important;
            }
        
            .woocommerce .iwptp-modal .cart .quantity {
                margin-left: 20px !important;
            }
        
            .iwptp-modal .cart .single_variation_wrap .single_variation {
                float: none !important;
            }
        
            .iwptp-product-form table.variations tr td {
                text-align: left
            }
        
            .iwptp-product-form table.variations tr td.label label {
                margin-top: 10px !important;
                display: inline-block !important;
            }'
        );
        break;
    case 'shopkeeper':
        wp_add_inline_style(
            'iwptp',
            '.iwptp-modal .cart select {
                height: 45px !important;
                font-size: 18px !important;
                line-height: 20px !important;
                font-weight: normal !important;
            }
        
            .iwptp-product-form table.variations tr td.label label {
                margin-top: 10px !important;
                display: inline-block !important;
            }'
        );
        break;
    case 'flatsome':
        wp_add_inline_style('iwptp', '
            .iwptp-product-form .woocommerce-variation-add-to-cart .plus,
            .iwptp-product-form .woocommerce-variation-add-to-cart .minus {
                display: none;
            }
        
            .iwptp-product-form .variations .reset_variations {
                position: relative !important;
                right: 0 !important;
                bottom: 0 !important;
                color: currentColor !important;
                opacity: 0.6;
                font-size: 11px;
                text-transform: uppercase;
            }
        
            .iwptp-product-form .cart .button,
            .iwptp .cart .button {
                margin-bottom: 0 !important;
            }
        ');
        break;
    case 'x':
        wp_add_inline_style('iwptp', '
            .iwptp-product-form input.input-text[type="number"] {
                height: 44px !important;
            }
        ');
        break;
    case 'woodmart':
        wp_add_inline_style('iwptp', '
            .iwptp-product-form .swatches-select {
                display: none !important;
            }
    
            .iwptp-product-form .woocommerce-variation-price .price {
                margin: 0 20px 0 0 !important;
            }
        
            .woodmart-products-shop-view {
                display: none !important;
            }
        
            div.quantity.iwptp-quantity-wrapper {
                font-size: 16px;    
            }
        ');
        break;
    case 'martfury':
        wp_add_inline_style('iwptp', '
        .iwptp-table {
            min-width: 100%;
        }
        ');
        break;
    case 'Divi':
        wp_add_inline_style('iwptp', '
            .iwptp-table {
                min-width: 100%;
            }
        
            .iwptp-add-to-cart-wrapper .quantity {
                width: auto !important;
            }
        
            .iwptp-add-to-cart-wrapper .quantity + button {
                vertical-align: middle !important;
            }
        
            .iwptp-product-form .woocommerce-variation-add-to-cart .button, .iwptp-product-form .button.button.single_add_to_cart_button,
            .iwptp-product-form .woocommerce-variation-add-to-cart .button:hover, .iwptp-product-form .button.button.single_add_to_cart_button:hover {
                padding: 12px 16px;
                height: auto !important;
                line-height: 1em !important; 
            }
            
            .iwptp-product-form .woocommerce-variation-add-to-cart .button:after, .iwptp-product-form .button.button.single_add_to_cart_button:after {
                display: none !important;
            }      
        ');
        break;
    case 'Avada':
        wp_add_inline_style('iwptp', '
            .iwptp-table {
                min-width: 100%;
            }
        
            body .iwptp-table input[type=number].qty {
                line-height: 17px !important;
                font-size: 14px !important;
                margin: 0 !important;
            }
        
            .iwptp-product-form .iwptp-quantity > input:not([type="number"]),
            .iwptp-table .iwptp-quantity > input:not([type="number"]) {
                display: none !important;
            }
        
            .iwptp-table .product-addon {
                width: 100% !important;
            }
        
            .iwptp-modal-content .woocommerce-variation.single_variation {
                display: none !important;
            }
        ');
        break;
    case 'equipo':
        wp_add_inline_style('iwptp', '
            .woocommerce-Tabs-panel .iwptp tr > th {
                width: auto !important;
                min-width: 0 !important;
            }
        ');
        break;
    case 'enfold':
        wp_add_inline_style('iwptp', '
            .iwptp-range-options-main input[type=number] {
                width: 60px !important;
                height: 36px !important;
                margin-right: 5px !important;
                margin-bottom: 0 !important;
                display: inline-block !important;
                padding: 0 0 0 5px !important;
            }
        
            .iwptp div form.cart div.quantity {
                float: none !important;
                margin: 0 5px 5px 0;
                white-space: nowrap;
                border: none;
                vertical-align: middle;
                min-width: 0;
                width: auto;
            }
        
            #top .iwptp form.cart .single_add_to_cart_button {
                float: none !important;
                margin-bottom: 5px;
                padding: 12px 30px;
                vertical-align: middle;
            }
        
            .iwptp-product-form .single_add_to_cart_button {
                border: 1px solid #c7c7c7;
            }
        
            .iwptp .single_variation_wrap, 
            .iwptp-product-form .single_variation_wrap {
                margin: 0 0 20px !important;
            }
        
            .iwptp .reset_variations, 
            .iwptp-product-form .reset_variations {
                line-height: 1em;
                font-size: 12px;
                position: relative;
                right: 0;
                bottom: 0;
                height: auto;
                margin-top: 1em;
                display: inline-block;
            }
        
        ');
        ob_start();
?>
        jQuery(function($){
        setTimeout(function(){
        $('.iwptp-quantity > .qty').attr('type', 'number');
        }, 200);

        // Enfold - add the + - buttons
        function iwptp_avia_apply_quant_btn(){
        $( ".iwptp .cart .quantity input[type=number], .iwptp-product-form .cart .quantity input[type=number]" ).each( function()
        {
        var number = $(this),
        current_val = number.val(),
        cloned = number.clone( true );

        // WC 4.0 renders '' for grouped products
        if( ( 'undefined' == typeof( current_val ) ) || ( '' == ( current_val + '' ).trim() ) )
        {
        var placeholder = cloned.attr( 'placeholder' );
        placeholder = ( ( 'undefined' == typeof( placeholder ) ) || ( '' == ( placeholder + '' ).trim() ) ) ? 1 : placeholder;
        cloned.attr( 'value', placeholder );
        }

        var max = parseFloat( number.attr( 'max' ) ),
        min = parseFloat( number.attr( 'min' ) ),
        step = parseInt( number.attr( 'step' ), 10 ),
        newNum = $( $( '
        <div />' ).append( cloned ).html().replace( 'number','text' ) ).insertAfter( number );
        number.remove();

        setTimeout(function(){
        if( newNum.next( '.plus' ).length === 0 )
        {
        var minus = $( '<input type="button" value="-" class="minus">' ).insertBefore( newNum ),
        plus = $( '<input type="button" value="+" class="plus">' ).insertAfter( newNum );

        minus.on( 'click', function()
        {
        var the_val = parseInt( newNum.val(), 10 ) - step;
        the_val = the_val < 0 ? 0 : the_val; the_val=the_val < min ? min : the_val; newNum.val(the_val).trigger( "change" ); }); plus.on( 'click' , function() { var the_val=parseInt( newNum.val(), 10 ) + step; the_val=the_val> max ? max : the_val;
            newNum.val(the_val).trigger( "change" );

            });
            }
            },10);

            });
            }

            $('body').on('iwptp_after_every_load', '.iwptp', iwptp_avia_apply_quant_btn);
            $('body').on('iwptp_product_form_ready', iwptp_avia_apply_quant_btn);
            })
        <?php
        wp_add_inline_script('iwptp', ob_get_clean(), 'after');
        break;

        //-- plumbin
    case 'plumbin':

        ob_start();
        ?>
            jQuery(function( $ ){
            function iwptp_plumbin_input_fix(){
            var $qty = $('.iwptp-quantity');
            $qty.each(function(){
            var $this = $(this),
            $input = $this.find('.qty'),
            $minus = $this.find('.iwptp-minus'),
            $input_grp = $this.find('.input-group');

            $input.attr('type', 'number');

            if( $input_grp.length ){
            $input.insertAfter($minus);
            $input_grp.remove();
            }
            })
            }

            iwptp_plumbin_input_fix();
            $('.iwptp').one('iwptp_layout', iwptp_plumbin_input_fix);
            setTimeout(iwptp_plumbin_input_fix, 1000);
            })
        <?php
        wp_add_inline_script('iwptp', ob_get_clean(), 'after');

        break;

        //-- bavarian
    case 'bavarian':
        ob_start();
        ?>
            jQuery(function($){
            $('body').on('click', 'a.iwptp-title, .iwptp-button-product_link, a.iwptp-product-image-wrapper, .iwptp-product-link', function(e){
            window.location = $(this).attr('href');
            setTimeout(function(){ $('.kt-preloader-obj').hide(); }, 1);
            })
            })
        <?php
        wp_add_inline_script('jquery', ob_get_clean(), 'after');

        break;


        //-- motor
    case 'motor':
        wp_add_inline_style('iwptp', '
            .iwptp + .row .stm-blog-pagination {
                display: none !important;
            }
        ');
        break;
        //-- riode
    case 'riode':
        ob_start();
        ?>
            jQuery(function($){
            var count = 10,
            clear = setInterval(function(){
            var $qty_plus = $('.iwptp-plus');
            $qty_plus.off('mousedown');
            --count;
            if( ! count ){
            clearInterval(clear);
            }
            }, 500);
            })
    <?php
        wp_add_inline_script('jquery', ob_get_clean(), 'after');
        break;

    default:
        // code...
        break;
}

// yith yraq
if (defined('YITH_YWRAQ_PREMIUM')) {
    wp_enqueue_script('iwptp-yith-ywraq', IWPTPL_PLUGIN_URL . 'assets/js/yith-ywraq.js', array('jquery'), IWPTPL_VERSION, true);
    wp_add_inline_script('iwptp-yith-ywraq', 'var iwptp_ywraq_url="' . YITH_Request_Quote()->get_raq_page_url() . '"', 'after');

    $iwptp_ywraq_ids = [];

    foreach (YITH_Request_Quote()->raq_content as $item) {
        if (!isset($item['variation_id'])) {
            $iwptp_ywraq_ids[] = $item['product_id'];
        } else if ($item['variation_id'] != 0) {
            $iwptp_ywraq_ids[] = $item['variation_id'];
        }
    }

    wp_add_inline_script('iwptp-yith-ywraq', 'var iwptp_ywraq_ids=' . wp_json_encode($iwptp_ywraq_ids) . '; ', 'after');

    wp_enqueue_style('iwptp-yith-ywraq', IWPTPL_PLUGIN_URL . 'assets/css/yith-ywraq.css', null, IWPTPL_VERSION);
}

// ultimate social media icons
if (defined('SFSI_DOCROOT')) {
    wp_enqueue_script('iwptp-ultimate-social-media-icons', IWPTPL_PLUGIN_URL . 'assets/js/ultimate-social-media-icons.js', array('jquery', 'SFSICustomJs'), IWPTPL_VERSION, true);
}

// product addons
if (class_exists('WC_Product_Addons_Helper')) {
    // fix tipTip error
    wp_enqueue_script('jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip.min.js', array('jquery'), WC_VERSION, true);
    // enqueue fixed addons script
    wp_register_script('accounting', WC()->plugin_url() . '/assets/js/accounting/accounting.min.js', array('jquery'), '0.4.2');

    wp_dequeue_script('woocommerce-addons');
    wp_enqueue_script('woocommerce-addons', IWPTPL_PLUGIN_URL . 'assets/js/addons.js', array('jquery', 'accounting'), IWPTPL_VERSION, true);

    $params = array(
        'price_display_suffix' => esc_attr(get_option('woocommerce_price_display_suffix')),
        'tax_enabled' => wc_tax_enabled(),
        'price_include_tax' => 'yes' === esc_attr(get_option('woocommerce_prices_include_tax')),
        'display_include_tax' => (wc_tax_enabled() && 'incl' === esc_attr(get_option('woocommerce_tax_display_shop'))) ? true : false,
        'ajax_url' => WC()->ajax_url(),
        'i18n_sub_total' => __('Subtotal', 'woocommerce-product-addons'),
        'i18n_remaining' => __('characters remaining', 'woocommerce-product-addons'),
        'currency_format_num_decimals' => absint(get_option('woocommerce_price_num_decimals')),
        'currency_format_symbol' => get_woocommerce_currency_symbol(),
        'currency_format_decimal_sep' => esc_attr(stripslashes(get_option('woocommerce_price_decimal_sep'))),
        'currency_format_thousand_sep' => esc_attr(stripslashes(get_option('woocommerce_price_thousand_sep'))),
        'trim_trailing_zeros' => apply_filters('woocommerce_price_trim_zeros', false),
        'is_bookings' => class_exists('WC_Bookings'),
        'trim_user_input_characters' => apply_filters('woocommerce_product_addons_show_num_chars', 1000),
        'quantity_symbol' => 'x ',
    );

    if (!function_exists('get_woocommerce_price_format')) {
        $currency_pos = get_option('woocommerce_currency_pos');
        switch ($currency_pos) {
            case 'left':
                $format = '%1$s%2$s';
                break;
            case 'right':
                $format = '%2$s%1$s';
                break;
            case 'left_space':
                $format = '%1$s&nbsp;%2$s';
                break;
            case 'right_space':
                $format = '%2$s&nbsp;%1$s';
                break;
        }
        $params['currency_format'] = esc_attr(str_replace(array('%1$s', '%2$s'), array('%s', '%v'), $format));
    } else {
        $params['currency_format'] = esc_attr(str_replace(array('%1$s', '%2$s'), array('%s', '%v'), get_woocommerce_price_format()));
    }

    wp_localize_script('woocommerce-addons', 'woocommerce_addons_params', apply_filters('woocommerce_product_addons_params', $params));
    wp_enqueue_style('woocommerce-addons-css', plugins_url() . '/woocommerce-product-addons/assets/css/frontend.css');
}
