<?php
if (!defined('ABSPATH')) {
    exit;
}

if (isset($gap)) {
    $style = " style='height:$gap;' ";
}
?>
<span class="iwptp-clear" <?php if (!empty($style)) echo esc_attr($style); ?>></span>