<?php
if (!defined('ABSPATH')) {
    exit;
}

$table_id = $GLOBALS['iwptp_table_data']['id'];
$field_name_results_per_page = $table_id . '_results_per_page';

// get default results_per_page params
$default_params = iwptp_get_nav_filter('results_per_page');

// ensure drop down options
if (empty($dropdown_options)) {
    $dropdown_options = json_decode('[{"results":"10","label":"10 per page"},{"results":"20","label":"20 per page"}]', true);
}

// current max posts_per_page limit
if (!empty($_GET[$field_name_results_per_page])) {
    $limit = &sanitize_text_field($_GET[$field_name_results_per_page]);
} else {
    if ($default_params) {
        $limit = $default_params['results'];
    } else {
        if (empty($GLOBALS['iwptp_table_data']['query']['limit'])) {
            $limit = 10;
        } else {
            $limit = $GLOBALS['iwptp_table_data']['query']['limit'];
        }
    }
}

// create a new dropdown option to accomodate limit if required
$new_op_required = true;
foreach ($dropdown_options as $option) {
    if ($option['results'] == $limit) {
        $new_op_required = false;
    }
}

if ($new_op_required) {
    $new_op = array(
        'results' => $limit,
        'label' => $limit . ' per page',
    );

    if (-1 == $limit) {
        $new_op['label'] = 'Show all results';
    }

    array_unshift($dropdown_options, $new_op);
}

if (empty($display_type)) {
    $display_type = 'dropdown';
}

if ($display_type == 'dropdown') {
    $container_html_class = 'iwptp-dropdown iwptp-filter ' . $html_class;
    $heading_html_class = 'iwptp-dropdown-label';
    $options_container_html_class = 'iwptp-dropdown-menu';
    $single_option_container_html_class = 'iwptp-dropdown-option';
} else {
    $container_html_class = 'iwptp-options-row iwptp-filter ' . $html_class;
    $heading_html_class = 'iwptp-options-heading';
    $options_container_html_class = 'iwptp-options';
    $single_option_container_html_class = 'iwptp-option';
}

// heading row
if (empty($heading)) {
    $heading = '';
}

if (!$heading = iwptp_parse_2($heading)) {
    $container_html_class .= ' iwptp-no-heading iwptp-filter-open';
} else {
    $heading = str_replace('[limit]', $limit, $heading);
}

if (!empty($accordion_always_open)) {
    $container_html_class .= ' iwptp-filter-open';
}

?>
<div class="<?php echo esc_attr($container_html_class); ?>" data-iwptp-filter="results_per_page" data-iwptp-heading_format__op_selected="only_selected">

    <?php
    ob_start();
    ?>

    <!-- options menu -->
    <div class="<?php echo esc_attr($options_container_html_class); ?>">
        <?php
        $selected_label = '';
        foreach ($dropdown_options as $option_index => $option) {

            if ($limit == $option['results']) {
                $checked = ' checked="checked" ';
                $selected_label = $option['label'];

                $filter_info = array(
                    'filter'  => 'results_per_page',
                    'results' => $option['results'],
                    'label'   => $option['label'],
                );
                iwptp_update_user_filters($filter_info, true);
            } else {
                $checked = '';
            }

        ?>
            <label class="<?php echo esc_attr($single_option_container_html_class); ?>">
                <input type="radio" name="<?php echo esc_attr($field_name_results_per_page); ?>" <?php echo esc_attr($checked); ?> value="<?php echo esc_attr($option['results']); ?>" class="iwptp-filter-radio"><span><?php echo esc_html($option['label']); ?></span>
            </label>
        <?php
        }

        ?>
    </div>

    <?php
    $dropdown_menu = ob_get_clean();
    ?>

    <div class="iwptp-filter-heading">
        <!-- label -->
        <span class="<?php echo esc_attr($heading_html_class); ?>">
            <span>
                <?php
                if ($heading) {
                    echo wp_kses($heading, iwptp_allowed_html_tags());
                } else {
                    echo wp_kses($selected_label, iwptp_allowed_html_tags());
                }
                ?>
            </span>
        </span>
        <!-- icon -->
        <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?>
    </div>

    <?php echo wp_kses($dropdown_menu, iwptp_allowed_html_tags()); ?>

</div>