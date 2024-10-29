<div class="iwptp-toggle-options" iwptp-model-key="modals">
    <div class="iwptp-editor-light-heading iwptp-toggle-label">Modals <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?></div>

    <div class="iwptp-editor-row-option" iwptp-model-key="labels">
        <label style="font-weight: bold;">
            For multiple translations enter one per line like this:
            <small style="line-height: 1.5;">
                Apply <br>
                en_US: Apply <br>
                fr_FR: Appliquer <br>
            </small>
            <small>
                Read more on documentation
            </small>
        </label>

        <div class="iwptp-editor-row-option">
            <label>
                'Filters'
            </label>
            <textarea iwptp-model-key="filters"></textarea>
        </div>

        <div class="iwptp-editor-row-option">
            <label>
                'Sort'
            </label>
            <textarea iwptp-model-key="sort"></textarea>
        </div>

        <div class="iwptp-editor-row-option">
            <label>
                'Reset'
            </label>
            <textarea iwptp-model-key="reset"></textarea>
        </div>

        <div class="iwptp-editor-row-option">
            <label>
                'Apply'
            </label>
            <textarea iwptp-model-key="apply"></textarea>
        </div>

    </div>
</div>