<?php
// fulfil search 
function iwptp_search($filter_info, &$post__in)
{

    if (empty($filter_info['keyword'])) {
        return;
    }

    $filter_info = apply_filters('iwptp_search_args', $filter_info);

    if (!empty($filter_info['use_default_search'])) {

        $search_terms = array_map('trim', explode(' ', $filter_info['keyword']));
        $sql          = [];

        global $wpdb;

        foreach ($search_terms as $term) {
            // Terms prefixed with '-' should be excluded.
            $include = '-' !== substr($term, 0, 1);

            if ($include) {
                $like_op  = 'LIKE';
                $andor_op = 'OR';
            } else {
                $like_op  = 'NOT LIKE';
                $andor_op = 'AND';
                $term     = substr($term, 1);
            }

            $like = '%' . $wpdb->esc_like($term) . '%';
            $sql[] = $wpdb->prepare("(($wpdb->posts.post_title $like_op %s) $andor_op ($wpdb->posts.post_excerpt $like_op %s) $andor_op ($wpdb->posts.post_content $like_op %s))", $like, $like, $like);
        }

        if (
            !empty($sql) &&
            !is_user_logged_in()
        ) {
            $sql[] = "($wpdb->posts.post_password = '')";
        }

        $sql = " SELECT ID FROM $wpdb->posts WHERE " . implode(' AND ', $sql);

        $post__in = $wpdb->get_col($sql); //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared 

        if (!$post__in) {
            $post__in = array('0');
        }

        return;
    }

    $search_ids = array(
        'title' => array(
            'phrase_exact' => [], // $keyword_phrase === $title
            'phrase_like' => [], // $title = ...$keyword_phrase...
            'keyword_exact' => [], // $title = $word1 $keyword $word2
            'keyword_like' => [], // $title = $word1 ...$keyword... $word2
        ),
        'sku' => array(
            'phrase_exact' => [],
            'phrase_like' => [],
            'keyword_exact' => [],
            'keyword_like' => [],
        ),
        'category' => array(
            'phrase_exact' => [],
            'phrase_like' => [],
            'keyword_exact' => [],
            'keyword_like' => [],
        ),
        'attribute' => array(
            'phrase_exact' => [],
            'phrase_like' => [],
            'keyword_exact' => [],
            'keyword_like' => [],
            'items' => [],
        ),
        'tag' => array(
            'phrase_exact' => [],
            'phrase_like' => [],
            'keyword_exact' => [],
            'keyword_like' => [],
        ),
        'content' => array(
            'phrase_exact' => [],
            'phrase_like' => [],
            'keyword_exact' => [],
            'keyword_like' => [],
        ),
        'excerpt' => array(
            'phrase_exact' => [],
            'phrase_like' => [],
            'keyword_exact' => [],
            'keyword_like' => [],
        ),
        'custom_field' => array(
            'phrase_exact' => [],
            'phrase_like' => [],
            'keyword_exact' => [],
            'keyword_like' => [],
            'items' => [],
        ),
    );

    $keyword_phrase = strtolower(trim($filter_info['keyword']));

    $settings = iwptp_get_settings_data();

    // replacements
    foreach (preg_split('/\r\n|\r|\n/', strtolower($settings['search']['replacements'])) as $line) {
        $split1 = array_map('trim', explode(':', $line));
        $correction = $split1[0];
        if (!empty($split1[1])) {
            $incorrect = array_map('trim', explode('|', $split1[1]));
            $keyword_phrase = str_replace($incorrect, $correction, $keyword_phrase);
        }
    }

    $keyword_separator = !empty($settings['search']['separator']) ? $settings['search']['separator'] : ' ';
    $stopwords = array_map('trim', explode(',', $settings['search']['stopwords']));
    $keywords = array_diff(explode($keyword_separator, $keyword_phrase), $stopwords);

    if (empty($filter_info['target'])) {
        $filter_info['target'] = array('title', 'content');
    }

    $filter_info['target'] = apply_filters('iwptp_search_target', $filter_info['target']);

    if (in_array('custom_field', $filter_info['target'])) {
        $custom_fields__custom_rules = [];
        $custom_fields__default_rules = [];

        if (!$settings['search']['custom_field']['items']) {
            $custom_fields__default_rules = $filter_info['custom_fields'];
        } else {
            foreach ($settings['search']['custom_field']['items'] as $item) {
                if (
                    !in_array($item['item'], $filter_info['custom_fields'])
                ) {
                    continue;
                }

                if ($item['custom_rules_enabled']) {
                    $custom_fields__custom_rules[] = $item['item'];
                } else {
                    $custom_fields__default_rules[] = $item['item'];
                }
            }
        }
    }

    if (in_array('attribute', $filter_info['target'])) {

        $attributes__custom_rules = [];
        $attributes__default_rules = [];

        if (!$settings['search']['attribute']['items']) {
            $attributes__default_rules = $filter_info['attributes'];
        } else {
            foreach ($settings['search']['attribute']['items'] as $item) {
                if (
                    !in_array($item['item'], $filter_info['attributes'])
                ) {
                    continue;
                }

                if ($item['custom_rules_enabled']) {
                    $attributes__custom_rules[] = $item['item'];
                } else {
                    $attributes__default_rules[] = $item['item'];
                }
            }
        }
    }

    global $wpdb;

    if (in_array('title', $filter_info['target'])) {
        $field = 'title';
        $item = null;

        $query = "
      SELECT ID 
      FROM $wpdb->posts 
      WHERE $wpdb->posts.post_type = 'product' 
      AND post_title 
    ";
        iwptp_search__query($field, $item, $query, $keyword_phrase, $keywords, $search_ids);
    }

    if (in_array('category', $filter_info['target'])) {
        $field = 'category';
        $item = null;

        $query = "
      SELECT $wpdb->term_relationships.object_id 
      FROM $wpdb->terms
      INNER JOIN $wpdb->term_taxonomy ON $wpdb->terms.term_id = $wpdb->term_taxonomy.term_id
      INNER JOIN $wpdb->term_relationships ON $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id
      WHERE $wpdb->term_taxonomy.taxonomy = 'product_cat' 
      AND name
    ";
        iwptp_search__query($field, $item, $query, $keyword_phrase, $keywords, $search_ids);
    }

    if (in_array('attribute', $filter_info['target'])) {
        $field = 'attribute';
        $item = null;

        $query = "
      SELECT $wpdb->term_relationships.object_id 
      FROM $wpdb->terms
      INNER JOIN $wpdb->term_taxonomy ON $wpdb->terms.term_id = $wpdb->term_taxonomy.term_id
      INNER JOIN $wpdb->term_relationships ON $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id
      WHERE $wpdb->term_taxonomy.taxonomy %s 
      AND name       
    ";

        // default rule items
        if (count($attributes__default_rules)) {
            $var = "IN ('pa_" . implode("','pa_", $attributes__default_rules) . "')";
            iwptp_search__query($field, null, sprintf($query, $var), $keyword_phrase, $keywords, $search_ids);
        }

        // custom rule items
        if (count($attributes__custom_rules)) {
            foreach ($attributes__custom_rules as $item) {
                $var = "= 'pa_$item'";
                iwptp_search__query($field, $item, sprintf($query, $var), $keyword_phrase, $keywords, $search_ids);
            }
        }
    }

    if (in_array('tag', $filter_info['target'])) {
        $field = 'tag';
        $item = null;

        $query = "
      SELECT $wpdb->term_relationships.object_id 
      FROM $wpdb->terms
      INNER JOIN $wpdb->term_taxonomy ON $wpdb->terms.term_id = $wpdb->term_taxonomy.term_id
      INNER JOIN $wpdb->term_relationships ON $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id
      WHERE $wpdb->term_taxonomy.taxonomy = 'product_tag' 
      AND name
    ";
        iwptp_search__query($field, $item, $query, $keyword_phrase, $keywords, $search_ids);
    }

    if (in_array('content', $filter_info['target'])) {
        $field = 'content';
        $item = null;

        $query = "
      SELECT ID 
      FROM $wpdb->posts 
      WHERE post_type = 'product' 
      AND post_content 
    ";
        iwptp_search__query($field, $item, $query, $keyword_phrase, $keywords, $search_ids);
    }

    if (in_array('excerpt', $filter_info['target'])) {
        $field = 'excerpt';
        $item = null;
        $query = "
      SELECT ID 
      FROM $wpdb->posts 
      WHERE post_type = 'product' 
      AND post_excerpt 
    ";
        iwptp_search__query($field, $item, $query, $keyword_phrase, $keywords, $search_ids);
    }

    if (in_array('sku', $filter_info['target'])) {
        $field = 'sku';
        $item = null;

        $query = "
      SELECT post_id 
      FROM $wpdb->postmeta 
      WHERE meta_key = '_sku'
      AND meta_value 
    ";
        iwptp_search__query($field, $item, $query, $keyword_phrase, $keywords, $search_ids);
    }

    if (in_array('custom_field', $filter_info['target'])) {
        $field = 'custom_field';
        $item = null;

        $query = "
      SELECT post_id 
      FROM $wpdb->postmeta 
      WHERE meta_key %s
      AND meta_value 
    ";

        // default rule items
        if (count($custom_fields__default_rules)) {
            $var = "IN ('" . implode("','", $custom_fields__default_rules) . "')";
            iwptp_search__query($field, null, sprintf($query, $var), $keyword_phrase, $keywords, $search_ids);
        }

        // custom rule items
        if (count($custom_fields__custom_rules)) {
            foreach ($custom_fields__custom_rules as $item) {
                $var = "= '$item'";
                iwptp_search__query($field, $item, sprintf($query, $var), $keyword_phrase, $keywords, $search_ids);
            }
        }
    }

    // custom taxonomy
    foreach ($filter_info['target'] as $target) {
        $target = strtolower(trim($target));
        if ('taxonomy__' === substr($target, 0, 10)) {
            $taxonomy = substr($target, 10);
            $item = null;

            $query = "
        SELECT $wpdb->term_relationships.object_id 
        FROM $wpdb->terms
        INNER JOIN $wpdb->term_taxonomy ON $wpdb->terms.term_id = $wpdb->term_taxonomy.term_id
        INNER JOIN $wpdb->term_relationships ON $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id
        WHERE $wpdb->term_taxonomy.taxonomy = '" . esc_sql($taxonomy) . "' 
        AND name         
      ";
            iwptp_search__query($target, $item, $query, $keyword_phrase, $keywords, $search_ids);
        }
    }

    iwptp_search__combine(apply_filters('iwptp_search_ids', $search_ids, $filter_info, $keyword_phrase, $keywords), $post__in);
}

// query and store post ids
function iwptp_search__query($field, $item, $query, $keyword_phrase, $keywords, &$search_ids)
{

    if (empty($search_ids[$field])) {
        $search_ids[$field] = array(
            'phrase_exact' => [],
            'phrase_like' => [],
            'keyword_exact' => [],
            'keyword_like' => [],
        );
    }

    $settings = iwptp_get_settings_data();

    $permitted = array(
        'phrase_like' => true,
        'phrase_exact' => true,
        'keyword_like' => true,
        'keyword_exact' => true,
    );

    if (empty($settings['search'][$field])) { // custom taxonomy
        $settings['search'][$field]['rules'] = array(
            'keyword_exact_enabled' => true,
            'keyword_exact_score'   => 40,
            'keyword_like_enabled'  => true,
            'keyword_like_score'    => 20,
            'phrase_exact_enabled'  => true,
            'phrase_exact_score'    => 100,
            'phrase_like_enabled'   => true,
            'phrase_like_score'     => 60,
        );
    }

    $rules = $settings['search'][$field]['rules'];

    if ($item) {
        foreach ($settings['search'][$field]['items'] as $_item) {
            if (
                $_item['item'] == $item &&
                $_item['custom_rules_enabled']
            ) {
                $rules = $_item['rules'];
            }
        }
    }

    foreach ($permitted as $key => &$val) {
        $val = $rules[$key . '_enabled'];
    }

    global $wpdb;

    if (!empty($item)) {
        $search_ids[$field]['items'][$item] = [];
        $location = &$search_ids[$field]['items'][$item];
    } else {
        $location = &$search_ids[$field];
    }

    if (empty($location)) {
        $location = array(
            'phrase_exact' => [],
            'phrase_like' => [],
            'keyword_exact' => [],
            'keyword_like' => [],
        );
    }

    // phrase exact
    if ($permitted['phrase_exact']) {
        $esc_keyword_phrase = esc_sql($keyword_phrase);
        $post_ids = $wpdb->get_col($query . " ='$esc_keyword_phrase' "); //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared 
        $location['phrase_exact'] = array_merge($location['phrase_exact'], $post_ids);
    }

    // phrase like
    if ($permitted['phrase_like']) {
        $esc_keyword_phrase = $wpdb->esc_like($keyword_phrase);
        $post_ids = $wpdb->get_col($query . " LIKE '%$esc_keyword_phrase%'"); //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared 
        $location['phrase_like'] = array_merge($location['phrase_like'], $post_ids);
    }

    foreach ($keywords as $k => $keyword) {

        // if( in_array($keyword, [])  ){
        //   continue;
        // }

        $esc_keyword = $wpdb->esc_like($keyword);

        // keyword exact
        if ($permitted['keyword_exact']) {
            $post_ids = $wpdb->get_col($query . " = '$esc_keyword'"); //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared 
            $location['keyword_exact'] = array_merge($location['keyword_exact'], $post_ids);

            // -- between
            $post_ids = $wpdb->get_col($query . " LIKE '% $esc_keyword %'"); //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared 
            $location['keyword_exact'] = array_merge($location['keyword_exact'], $post_ids);

            // -- starting
            $post_ids = $wpdb->get_col($query . " LIKE '$esc_keyword %'"); //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared 
            $location['keyword_exact'] = array_merge($location['keyword_exact'], $post_ids);

            // -- ending
            $post_ids = $wpdb->get_col($query . " LIKE '% $esc_keyword'"); //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared 
            $location['keyword_exact'] = array_merge($location['keyword_exact'], $post_ids);
        }

        // keyword like
        if ($permitted['keyword_like']) {
            $post_ids = $wpdb->get_col($query . " LIKE '%$esc_keyword%'"); //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared 
            $location['keyword_like'] = array_merge($location['keyword_like'], $post_ids);
        }
    }
}

// combine current search ids into the query post__in 
function iwptp_search__combine($search_ids, &$post__in)
{
    // restrict to search results
    if (is_array($search_ids)) {

        $arr = [];
        $settings = iwptp_get_settings_data();
        $search_settings = $settings['search'];

        foreach ($search_ids as $field => $matches) {
            foreach ($matches as $match_type => $ids) {
                if (empty($settings['search'][$field])) { // custom taxonomy
                    $rules = array(
                        'keyword_exact_enabled' => true,
                        'keyword_exact_score'   => 40,
                        'keyword_like_enabled'  => true,
                        'keyword_like_score'    => 20,
                        'phrase_exact_enabled'  => true,
                        'phrase_exact_score'    => 100,
                        'phrase_like_enabled'   => true,
                        'phrase_like_score'     => 60,
                    );
                } else {
                    $rules = $search_settings[$field]['rules'];
                }

                if ($match_type === 'items') {
                    foreach ($matches['items'] as $item => $matches) {
                        $item_rules = $rules;
                        // maybe use custom rules
                        foreach ($search_settings[$field]['items'] as $item2) {
                            if (
                                $item2['item'] === $item &&
                                !empty($item2['custom_rules_enabled'])
                            ) {
                                $item_rules = $item2['rules'];
                                break;
                            }
                        }

                        foreach ($matches as $match_type => $ids) {
                            foreach ($ids as $id) {
                                if (!isset($arr[$id])) {
                                    $arr[$id] = 0;
                                }
                                $arr[$id] += $item_rules[$match_type . '_score'];
                            }
                        }
                    }
                } else {
                    foreach ($ids as $id) {
                        if (!isset($arr[$id])) {
                            $arr[$id] = 0;
                        }
                        $arr[$id] += $rules[$match_type . '_score'];
                    }
                }
            }
        }

        arsort($arr);

        $post_ids = array_keys($arr);

        if (empty($post_ids)) {
            $post__in = array(0);
        } else if (empty($post__in)) {
            $post__in = $post_ids;
        } else {
            $post__in = array_intersect($post__in, $post_ids);

            if (!count($post__in)) {
                // if 1 search instance fails, fail all
                $post__in = array(0);
            }
        }
    }
}

// search data

$IWPTPL_SEARCH_DATA = array(

    'stopwords' => "i, me, my, myself, we, our, ours, ourselves, you, your, yours, yourself, yourselves, he, him, his, himself, she, her, hers, herself, it, its, itself, they, them, their, theirs, themselves, what, which, who, whom, this, that, these, those, am, is, are, was, were, be, been, being, have, has, had, having, do, does, did, doing, a, an, the, and, but, if, or, because, as, until, while, of, at, by, for, with, about, against, between, into, through, during, before, after, above, below, to, from, up, down, in, out, on, off, over, under, again, further, then, once, here, there, when, where, why, how, all, any, both, each, few, more, most, other, some, such, no, nor, not, only, own, same, so, than, too, very, s, t, can, will, just, don, should, now",

    'replacements' => '',

    'override_settings' => array(
        'target' => array('title', 'content'),
        'attributes' => [],
        'custom_fields' => [],
    ),

    'title' => array(
        'enabled' => true,
        'rules' => array(
            'phrase_exact_enabled' => true,
            'phrase_exact_score' => 100,

            'phrase_like_enabled' => true,
            'phrase_like_score' => 60,

            'keyword_exact_enabled' => true,
            'keyword_exact_score' => 40,

            'keyword_like_enabled' => true,
            'keyword_like_score' => 20,
        )
    ),
    'sku' => array(
        'enabled' => true,
        'rules' => array(
            'phrase_exact_enabled' => true,
            'phrase_exact_score' => 100,

            'phrase_like_enabled' => true,
            'phrase_like_score' => 60,

            'keyword_exact_enabled' => true,
            'keyword_exact_score' => 40,

            'keyword_like_enabled' => true,
            'keyword_like_score' => 20,
        )
    ),
    'category' => array(
        'enabled' => true,
        'rules' => array(
            'phrase_exact_enabled' => true,
            'phrase_exact_score' => 100,

            'phrase_like_enabled' => true,
            'phrase_like_score' => 60,

            'keyword_exact_enabled' => true,
            'keyword_exact_score' => 40,

            'keyword_like_enabled' => true,
            'keyword_like_score' => 20,
        )
    ),
    'attribute' => array(
        'enabled' => true,
        'rules' => array(
            'phrase_exact_enabled' => true,
            'phrase_exact_score' => 100,

            'phrase_like_enabled' => true,
            'phrase_like_score' => 60,

            'keyword_exact_enabled' => true,
            'keyword_exact_score' => 40,

            'keyword_like_enabled' => true,
            'keyword_like_score' => 20,
        ),
        'items' => []
    ),
    'tag' => array(
        'enabled' => true,
        'rules' => array(
            'phrase_exact_enabled' => true,
            'phrase_exact_score' => 100,

            'phrase_like_enabled' => true,
            'phrase_like_score' => 60,

            'keyword_exact_enabled' => true,
            'keyword_exact_score' => 40,

            'keyword_like_enabled' => true,
            'keyword_like_score' => 20,
        )
    ),
    'content' => array(
        'enabled' => true,
        'rules' => array(
            'phrase_exact_enabled' => true,
            'phrase_exact_score' => 100,

            'phrase_like_enabled' => true,
            'phrase_like_score' => 60,

            'keyword_exact_enabled' => true,
            'keyword_exact_score' => 40,

            'keyword_like_enabled' => true,
            'keyword_like_score' => 20,
        )
    ),
    'excerpt' => array(
        'enabled' => true,
        'rules' => array(
            'phrase_exact_enabled' => true,
            'phrase_exact_score' => 100,

            'phrase_like_enabled' => true,
            'phrase_like_score' => 60,

            'keyword_exact_enabled' => true,
            'keyword_exact_score' => 40,

            'keyword_like_enabled' => true,
            'keyword_like_score' => 20,
        )
    ),
    'custom_field' => array(
        'enabled' => true,
        'rules' => array(
            'phrase_exact_enabled' => true,
            'phrase_exact_score' => 100,

            'phrase_like_enabled' => true,
            'phrase_like_score' => 60,

            'keyword_exact_enabled' => true,
            'keyword_exact_score' => 40,

            'keyword_like_enabled' => true,
            'keyword_like_score' => 20,
        ),
        'items' => []
    ),
);


// search orderby
add_filter('iwptp_before_apply_user_filters', 'iwptp_search_orderby');
function iwptp_search_orderby()
{
    $table_data = iwptp_get_table_data();
    $table_id = $table_data['id'];
    $query = $table_data['query'];
    $sc_attrs = $query['sc_attrs'];

    if (
        empty($sc_attrs['search_orderby']) ||
        !(
            (!empty($_GET[$table_id . '_orderby']) &&
                $_GET[$table_id . '_orderby'] == 'search_orderby'
            ) ||
            (!empty($sc_attrs['_archive']) &&
                !empty($sc_attrs['_search']) &&
                (empty($_GET[$table_id . '_orderby']) ||
                    $_GET[$table_id . '_orderby'] === 'search_orderby'
                )
            )
        )
    ) {
        return;
    }

    $default_params = iwptp_get_nav_filter('orderby');

    if (is_numeric($sc_attrs['search_orderby'])) { // user entered an op index for orderby
        $sort_by = iwptp_get_nav_elms_ref('sort_by', $table_data);
        $op_index = (int) $sc_attrs['search_orderby'] - 1;

        if (
            gettype($sort_by) === 'array' &&
            !empty($sort_by[0]['dropdown_options'][$op_index])
        ) {
            $sort_by_op = $sort_by[0]['dropdown_options'][$op_index];
            $orderby = $sort_by_op['orderby'];
            $order = $sort_by_op['order'];
            $meta_key = $sort_by_op['meta_key'];
        }
    } else { // user entered an orderby val 
        $orderby = strtolower($sc_attrs['search_orderby']);
        $order = !empty($sc_attrs['search_order']) ? strtolower($sc_attrs['search_order']) : $default_params['order'];
        $meta_key = !empty($sc_attrs['search_meta_key']) ? $sc_attrs['search_meta_key'] : $default_params['meta_key'];
    }

    if (
        $orderby === 'price' &&
        $order === 'desc'
    ) {
        $orderby = 'price-desc';
        $order = 'DESC';
    }

    if ($orderby === 'initial') {
        $search_orderby_filter = array(
            'filter' => 'orderby',
            'orderby' => !empty($query['orderby']) ? $query['orderby'] : '',
            'order' => $query['order'] ? $query['order'] : '',
            'meta_key' => $query['meta_key'] ? $query['meta_key'] : '',
        );
    } else {
        $search_orderby_filter = array(
            'filter' => 'orderby',
            'orderby' => $orderby,
            'order' => $order,
            'meta_key' => $meta_key,
        );
    }

    iwptp_update_user_filters($search_orderby_filter, true);
    unset($_GET[$table_id . '_orderby']);
}
