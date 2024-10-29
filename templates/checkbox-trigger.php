<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('WC')) {
    return;
}

if (empty($link)) {
    $url = '';
} else if ($link === 'cart') {
    $url = wc_get_cart_url();
} else if ($link === 'checkout') {
    $url = wc_get_checkout_url();
} else if ($link === 'refresh') {
    global $wp;
    $url = home_url($wp->request);
}

$iwptp_settings = iwptp_get_settings_data();
$settings = $iwptp_settings['checkbox_trigger'];

$labels = isset($settings['labels']) ? $settings['labels'] : '';
$locale = get_locale();

$strings = [];

if (!empty($labels)) {
    foreach ($labels as $key => $translations) {
        $strings[$key] = [];
        $translations = preg_split('/$\R?^/m', $translations);
        foreach ($translations as $translation) {
            $array = explode(':', $translation);
            if (!empty($array[1])) {
                $strings[$key][trim($array[0])] = trim($array[1]);
            } else {
                $strings[$key]['default'] = stripslashes(trim($array[0]));
            }
        }
    }
}

foreach ($strings as $item => &$translations) {
    if (empty($translations[$locale])) {
        if (!empty($translations['default'])) {
            $translations[$locale] = $translations['default'];
        } else if (!empty($translations['en_US'])) {
            $translations[$locale] = $translations['en_US'];
        }
    }
}

ob_start();
?>
<style media="screen">
    @media(min-width:1200px) {
        .iwptp-cart-checkbox-trigger {
            display: <?php echo (isset($toggle) && $toggle == 'enabled') ? 'inline-block' : 'none !important'; ?>;
        }
    }

    @media(max-width:1100px) {
        .iwptp-cart-checkbox-trigger {
            display: <?php echo (isset($r_toggle) && $r_toggle == 'enabled') ? 'inline-block' : 'none !important'; ?>;
        }
    }
</style>
<?php
$style = ob_get_clean();

if (empty($strings['label'][$locale])) {
    $strings['label'][$locale] = '';
}

?>
<script type="text/template" id="tmpl-iwptp-cart-checkbox-trigger">
    <div 
		class="iwptp-cart-checkbox-trigger"
		data-iwptp-redirect-url="<?php echo !empty($url) ? esc_url($url) : ''; ?>"
	>
		<?php
        echo wp_kses($style, iwptp_allowed_html_tags());
        echo wp_kses(str_replace('[n]', '<span class="iwptp-total-selected"></span>', $strings['label'][$locale]), iwptp_allowed_html_tags());
        ?>
	</div>
</script>