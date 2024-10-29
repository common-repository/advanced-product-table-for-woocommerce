<?php
function iwptp_include_taxonomy_walker()
{
    if (!class_exists('IWPTPL_Taxonomy_Walker')) {
        class IWPTPL_Taxonomy_Walker extends Walker
        {
            var $db_fields = array('parent' => 'parent', 'id' => 'term_id');
            var $args;

            function __construct($args)
            {
                if (empty($args)) {
                    $args = [];
                }

                if (empty($args['taxonomy'])) {
                    $args['taxonomy'] = 'product_cat';
                }

                if (!$args['taxonomy_obj'] = get_taxonomy($args['taxonomy'])) {
                    return false;
                }

                if (!empty($args['exclude'])) {
                    $exclude_term_ids = [];
                    foreach ($args['exclude'] as $term_name) {
                        if ($term = get_term_by('name', $term_name, $args['taxonomy'])) {
                            $exclude_term_ids[] = $term->term_id;
                        }
                    }
                    $args['exclude'] = $exclude_term_ids;
                } else {
                    $args['exclude'] = [];
                }

                if (empty($args['single'])) {
                    $args['single'] = false;
                }

                if (empty($args['hide_empty'])) {
                    $args['hide_empty'] = false;
                }

                if (!isset($args['pre_open_depth'])) {
                    $args['pre_open_depth'] = 1;
                }

                if (!isset($args['option_class'])) {
                    $args['option_class'] = 'iwptp-dropdown-option';
                }

                if (empty($args['redirect'])) {
                    $args['redirect'] = false;
                }

                if (empty($args['category'])) {
                    $args['category'] = false;
                }

                if (empty($args['_field_name'])) {
                    $args['_field_name'] = $args['field_name'];
                }

                $this->args = $args;
            }

            public function walk($elements, $max_depth, ...$args)
            {
                $output = '';

                if ($max_depth < -1 || empty($elements)) {
                    return $output;
                }

                $parent_field = $this->db_fields['parent'];

                if (-1 == $max_depth) {
                    $empty_array = [];
                    foreach ($elements as $e) {
                        $this->display_element($e, $empty_array, 1, 0, $args, $output);
                    }
                    return $output;
                }

                /* iwptp modified begins */
                $term_ids = array_column($elements, 'term_id');

                $top_level_elements = [];
                $children_elements  = [];
                foreach ($elements as $e) {
                    if (
                        empty($e->$parent_field) ||
                        !in_array($e->$parent_field, $term_ids)
                    ) {
                        $top_level_elements[] = $e;
                    } else {
                        $children_elements[$e->$parent_field][] = $e;
                    }
                }
                /* iwptp modified ends */

                if (empty($top_level_elements)) {

                    $first = array_slice($elements, 0, 1);
                    $root  = $first[0];

                    $top_level_elements = [];
                    $children_elements  = [];
                    foreach ($elements as $e) {
                        if ($root->$parent_field == $e->$parent_field) {
                            $top_level_elements[] = $e;
                        } else {
                            $children_elements[$e->$parent_field][] = $e;
                        }
                    }
                }

                foreach ($top_level_elements as $e) {
                    $this->display_element($e, $children_elements, $max_depth, 0, $args, $output);
                }

                if (($max_depth == 0) && count($children_elements) > 0) {
                    $empty_array = [];
                    foreach ($children_elements as $orphans) {
                        foreach ($orphans as $op) {
                            $this->display_element($op, $empty_array, 1, 0, $args, $output);
                        }
                    }
                }

                return $output;
            }

            function start_el(&$output, $category, $depth = 0, $args = [], $id = 0)
            {
                $category = (object) $category;
                $children = get_terms($this->args['taxonomy'], array(
                    'parent' => $category->term_id,
                    'hide_empty' => 0,
                    'exclude' => $this->args['exclude'],
                    'fields' => 'ids',
                ));

                $has_children = false;
                $child_checked = false;
                if (!is_wp_error($children) && count($children)) {

                    $has_children = true;
                    $child_checked = false;
                    if (
                        !empty($_GET[$this->args['_field_name']]) &&
                        count(array_intersect(get_term_children($category->term_id, $this->args['taxonomy']), $_GET[$this->args['_field_name']]))
                    ) {
                        $child_checked = true;
                    }
                }

                $checked = false;
                if (
                    !empty($_GET[$this->args['_field_name']]) &&
                    in_array($category->term_taxonomy_id, $_GET[$this->args['_field_name']])
                ) {
                    $checked = true;
                    // use filter in query
                    $filter_info = array(
                        'filter' => ($this->args['taxonomy'] == 'product_cat') ? 'category' : 'taxonomy',
                        'taxonomy' => $this->args['taxonomy'],
                        'values' => array($category->term_taxonomy_id),
                        'operator' => !empty($this->args['operator']) ? $this->args['operator'] : 'IN',
                        'clear_label' => $this->args['taxonomy_obj']->labels->singular_name,
                    );

                    if (!empty($category->clear_label)) {
                        $filter_info['clear_labels_2'] = array(
                            $category->value => str_replace(
                                array('[option]', '[filter]'),
                                array($category->name, $this->args['taxonomy_obj']->labels->singular_name),
                                $category->clear_label
                            ),
                        );
                    } else {
                        $filter_info['clear_labels_2'] = array(
                            $category->value => $this->args['taxonomy_obj']->labels->singular_name . ' : ' . $category->name,
                        );
                    }

                    iwptp_update_user_filters($filter_info, $this->args['single']);
                }

                ob_start();

                if ($this->args['redirect']) {

                    if ($this->args['category'] == $category->slug) :
?>
                        <div class="iwptp-dropdown-option iwptp-current-term <?php echo esc_attr($this->args['option_class']); ?> <?php echo $has_children ? 'iwptp-accordion' : ''; ?> <?php echo ($checked || $child_checked) ? 'iwptp-ac-open' : ''; ?> <?php echo $this->args['pre_open_depth'] > $depth ? 'iwptp-ac-open' : ''; ?>" data-iwptp-value="<?php echo esc_attr($category->term_taxonomy_id); ?>" data-iwptp-open="<?php echo esc_attr($this->args['pre_open_depth']); ?>" data-iwptp-depth="<?php echo esc_attr($depth); ?>">
                            <label class="<?php echo $checked ? 'iwptp-active' : ''; ?>" data-iwptp-value="<?php echo esc_attr($category->term_taxonomy_id); ?>" data-iwptp-slug="<?php echo esc_attr($category->slug); ?>">
                                <?php echo esc_html($category->label); ?>
                                <?php echo $has_children ? wp_kses(iwptp_icon('chevron-down', 'iwptp-ac-icon'), iwptp_allowed_html_tags()) : ''; ?>
                            </label>
                        <?php else : ?>
                            <div class="iwptp-nav-redirect-option <?php echo esc_attr($this->args['option_class']); ?> <?php echo $has_children ? 'iwptp-accordion' : ''; ?> <?php echo ($checked || $child_checked) ? 'iwptp-ac-open' : ''; ?> <?php echo $this->args['pre_open_depth'] > $depth ? 'iwptp-ac-open' : ''; ?>" data-iwptp-value="<?php echo esc_attr($category->term_taxonomy_id); ?>" data-iwptp-open="<?php echo esc_attr($this->args['pre_open_depth']); ?>" data-iwptp-depth="<?php echo esc_attr($depth); ?>">
                                <label class="<?php echo $checked ? 'iwptp-active' : ''; ?>" data-iwptp-value="<?php echo esc_attr($category->term_taxonomy_id); ?>" data-iwptp-slug="<?php echo esc_attr($category->slug); ?>">
                                    <a href="<?php echo esc_url(strtok(get_term_link($category->term_taxonomy_id), '?') . iwptp_get_archive_query_string('category', $category->term_taxonomy_id)); ?>" class="iwptp-nav-redirect-link">
                                        <?php echo esc_html($category->label); ?>
                                    </a>
                                    <?php echo $has_children ? wp_kses(iwptp_icon('chevron-down', 'iwptp-ac-icon'), iwptp_allowed_html_tags()) : ''; ?>
                                </label>
                            <?php
                        endif;
                    } else {
                            ?>
                            <div class="<?php echo esc_attr($this->args['option_class']); ?> <?php echo $has_children ? 'iwptp-accordion' : ''; ?> <?php echo ($checked || $child_checked) ? 'iwptp-ac-open' : ''; ?> <?php echo $this->args['pre_open_depth'] > $depth ? 'iwptp-ac-open' : ''; ?>" data-iwptp-value="<?php echo esc_attr($category->term_taxonomy_id); ?>" data-iwptp-open="<?php echo esc_attr($this->args['pre_open_depth']); ?>" data-iwptp-depth="<?php echo esc_attr($depth); ?>">
                                <label class="<?php echo $checked ? 'iwptp-active' : ''; ?>" data-iwptp-value="<?php echo esc_attr($category->term_taxonomy_id); ?>" data-iwptp-slug="<?php echo esc_attr($category->slug); ?>">
                                    <input class="<?php echo (is_wp_error($children) || !count($children)) ? '' : 'iwptp-hr-parent-term'; ?>" type="<?php echo $this->args['single'] ? 'radio' : 'checkbox'; ?>" name="<?php echo esc_attr($this->args['field_name']); ?>[]" value="<?php echo esc_attr($category->term_taxonomy_id); ?>" <?php echo $checked ? ' checked="checked" ' : ''; ?> /><span><?php echo esc_html($category->label); ?></span>
                                    <?php echo $has_children ? wp_kses(iwptp_icon('chevron-down', 'iwptp-ac-icon'), iwptp_allowed_html_tags()) : ''; ?>
                                </label>
            <?php

                    }

                    $output .= ob_get_clean();
                }

                function end_el(&$output, $object, $depth = 0, $args = [])
                {
                    $output .= '</div>';
                }

                function start_lvl(&$output, $depth = 0, $args = [])
                {
                    $output .= '<div class="iwptp-hr-child-terms-wrapper iwptp-dropdown-sub-menu iwptp-ac-content">';
                }

                function end_lvl(&$output, $depth = 0, $args = [])
                {
                    $output .= '</div>';
                }
            }
        }
    }
