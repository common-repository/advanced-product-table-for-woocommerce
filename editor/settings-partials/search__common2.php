<!-- <?php echo esc_html($field); ?> -->
<div class="iwptp-editor-row-option iwptp-toggle-options iwptp-search__field" iwptp-model-key="<?php echo esc_attr($field); ?>" iwptp-controller="search_rules">
    <div class="iwptp-editor-light-heading iwptp-toggle-label">
        <?php echo esc_html($heading); ?> rules <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?>
    </div>
    <?php include('search__rules.php');  ?>
</div>