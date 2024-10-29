<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

?>
<thead>
    <?php
    $headings_mkp = '';
    $hide_headings = true;
    if (!empty($columns)) {
        foreach ($columns as $column_index => $column) {
            $GLOBALS['iwptp_col_index'] = $column_index;
            iwptp_parse_style_2($column['heading']);
            $col_id = 'iwptp-' . $column['heading']['id'];
            $curr_heading_mkp = iwptp_parse_2($column['heading']['content']);
            if ($curr_heading_mkp) {
                $hide_headings = false;
            }
            $headings_mkp .= '<th class="iwptp-heading ' . $col_id . '" data-iwptp-column-index="' . $column_index . '">' . $curr_heading_mkp . '</th>';
        }
    }
    ?>
    <tr class="iwptp-heading-row <?php echo $hide_headings ? 'iwptp-hide' : ''; ?>">
        <?php
        do_action('iwptp_after_heading_row_open');
        echo wp_kses($headings_mkp, iwptp_allowed_html_tags());
        do_action('iwptp_after_heading_row_close');
        ?>
    </tr>
</thead>
<?php
