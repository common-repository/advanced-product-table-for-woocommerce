<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

$data = iwptp_get_table_data();

$html_class = trim(apply_filters('iwptp_container_html_class', 'iwptp iwptp-' . $data['id'] . ' ' . trim($this->attributes['class'])));
if (!empty($GLOBALS['iwptp_table_data']['settings']) && is_array($GLOBALS['iwptp_table_data']['settings'])) {
    foreach ($GLOBALS['iwptp_table_data']['settings'] as $key => $value) {
        if ($value !== false && $value != '') {
            $GLOBALS['iwptp_table_data']['query']['sc_attrs'][$key] = $value;
        }
    }
}

ob_start();
?>
data-iwptp-table-id="<?php echo esc_attr($data['id']); ?>"
data-iwptp-query-string="<?php echo esc_attr(iwptp_get_table_query_string()); ?>"
data-iwptp-sc-attrs="<?php echo esc_attr(wp_json_encode($GLOBALS['iwptp_table_data']['query']['sc_attrs'])); ?>"
<?php
$attributes = apply_filters('iwptp_container_html_attributes', ob_get_clean());
$container_class = (!empty($data['style']['html_class'])) ? $data['style']['html_class'] : '';
?>
<div class="iwptp-table-container <?php echo esc_attr($container_class); ?>">
    <div id="iwptp-<?php echo esc_attr($data['id']); ?>" class="<?php echo esc_attr($html_class); ?>" <?php echo wp_kses($attributes, iwptp_allowed_html_tags()); ?>>