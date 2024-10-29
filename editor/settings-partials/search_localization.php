<div class="iwptp-toggle-options iwptp-search-rules" iwptp-model-key="search_localization">

    <div class="iwptp-editor-light-heading iwptp-toggle-label">Search <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?></div>

    <div class="">
        <div class="iwptp-editor-row-option">
            <label>
                <strong>'Sort by relevance' label</strong>
                <small>For multiple translations enter one per line like this:</small>
                <small>
                    Sort by relevance <br>
                    en_US: Sort by relevance<br>
                    fr_FR: Trier par pertinence <br>
                </small>
            </label>
            <textarea iwptp-model-key="relevance_label"></textarea>
        </div>
    </div>
</div>