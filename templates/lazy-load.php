<?php
$data = iwptp_get_table_data();
$sc_attrs = $data['query']['sc_attrs'];
unset($sc_attrs['lazy_load']);
if (count($sc_attrs)) {
    $sc_attrs = ' data-iwptp-sc-attrs="' . esc_attr(wp_json_encode($sc_attrs)) . '" ';
} else {
    $sc_attrs = '';
}
?>
<div class="iwptp-lazy-load" data-iwptp-table-id="<?php echo esc_attr($data['id']); ?>" <?php echo esc_attr($sc_attrs); ?>>
    <div class="iwptp-ll-anim"></div>
</div>