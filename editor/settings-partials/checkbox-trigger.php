<div class="iwptp-toggle-options" iwptp-model-key="checkbox_trigger">
    <div class="iwptp-editor-light-heading iwptp-toggle-label">Checkbox trigger <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?></div>

    <div class="iwptp-editor-row-option" iwptp-model-key="labels">
        <label style="font-weight: bold;">
            For multiple translations enter one per line like this:
            <small>
                Add selected ([n]) to cart <br>
                en_US: Add selected ([n]) to cart <br>
                fr_FR: Ajouter des produits ([n]) au panier <br>
            </small>
            <small>Use placeholder [n] for number of checked items</small>
            <small>
                Read more on documentation
            </small>
        </label>
        <textarea iwptp-model-key="label"></textarea>
    </div>
</div>