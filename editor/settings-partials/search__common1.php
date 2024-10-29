<!-- <?php echo esc_html($field); ?> -->
<div class="iwptp-editor-row-option iwptp-toggle-options iwptp-search__field" iwptp-model-key="" iwptp-controller="">

    <div class="iwptp-editor-light-heading iwptp-toggle-label">
        <?php echo esc_html($heading); ?> rules <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?>
    </div>

    <?php include('search__rules.php');  ?>

    <div class="iwptp-editor-row-option" iwptp-model-key="items">
        <div class="iwptp-editor-row-option iwptp-toggle-options iwptp-search__field" iwptp-model-key="[]" iwptp-model-key-index="0" iwptp-row-template="" iwptp-controller="">
            <div class="iwptp-editor-light-heading iwptp-toggle-label">
                <span iwptp-content-template="label"></span> <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?>
            </div>

            <input type="hidden" iwptp-model-key="" disabled />

            <div class="iwptp-editor-row-option">
                <label>
                    <input type="checkbox" iwptp-model-key="" disabled> Custom relevance rules
                </label>
            </div>

            <div class="iwptp-editor-row-option iwptp-search-rules__custom-rules" iwptp-panel-condition="prop" iwptp-condition-prop="" iwptp-condition-val="">
                <?php include('search__rules.php');  ?>
            </div>
        </div>

    </div>
</div>