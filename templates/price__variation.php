<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

echo '<span class="iwptp-price-wrapper '. esc_attr($html_class) .'">' . wp_kses($product->get_price_html(), iwptp_allowed_html_tags()) . '</span>';
