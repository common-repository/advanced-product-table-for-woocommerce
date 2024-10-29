<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( ! $name ) {
	return;
}

$style = '';

if( ! empty( $thickness ) ){
	$style .= 'stroke-width:' . $thickness . ';';
}

if( ! empty( $color ) ){
	$style .= 'color:' . $color . ';';
}

if( ! empty( $size ) ){
	$style .= 'font-size:' . $size . ';';
}

if( empty( $tooltip ) ){
	$tooltip = '';
}

if( empty( $title ) ){
	$title = '';
}

iwptp_icon($name, $html_class . ' iwptp-feather-icon', $style, $tooltip, $title);
