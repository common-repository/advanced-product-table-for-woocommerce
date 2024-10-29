<div class="iwptp-toggle-options" iwptp-model-key="cart_widget">
    <div class="iwptp-editor-light-heading iwptp-toggle-label">Cart widget <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?></div>

    <div class="iwptp-editor-row-option" iwptp-model-key="labels">
        <label style="font-weight: bold;">
            For multiple translations enter one per line like this:
            <small>
                Item <br>
                en_US: Item <br>
                fr_FR: Article <br>
            </small>
            <small>
                Read more on documentation
            </small>
        </label>

        <div class="iwptp-editor-row-option">
            <label>
                'Item' (singular)
            </label>
            <textarea iwptp-model-key="item"></textarea>
        </div>

        <div class="iwptp-editor-row-option">
            <label>
                'Items' (plural)
            </label>
            <textarea iwptp-model-key="items"></textarea>
        </div>

        <div class="iwptp-editor-row-option">
            <label>
                'View Cart'
            </label>
            <textarea iwptp-model-key="view_cart"></textarea>
        </div>

        <div class="iwptp-editor-row-option">
            <label>
                'Extra charges may apply'
            </label>
            <textarea iwptp-model-key="extra_charges"></textarea>
        </div>
    </div>

</div>