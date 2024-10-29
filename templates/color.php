<?php
if (!defined('ABSPATH')) {
    exit;
}

if (empty($color)) {
    $color = 'black';
}

if (empty($shape)) {
    $shape = 'circle';
}

if (empty($size)) {
    $dim = '';
} else {
    $dim = ' width:' . $size . '; height:' . $size . ';';
}

if (empty($tooltip)) {
    $tooltip = '';
}

?>
<span class="iwptp-color iwptp-tooltip-parent iwptp-shape-<?php echo esc_attr($shape); ?>" style="background: <?php echo esc_attr($color); ?>; <?php echo esc_attr($dim); ?>">
    <span class="iwptp-tooltip"><?php echo esc_html($tooltip); ?></span>
</span>