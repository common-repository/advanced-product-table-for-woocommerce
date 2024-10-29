<div class="iwptp-toggle-options iwptp-search-rules" iwptp-model-key="search">

    <div class="iwptp-editor-light-heading iwptp-toggle-label">
        Search <span class="iwptp-pro-badge" title="For Pro Version">Pro</span>
        <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?>
    </div>

    <div class="">
        <!-- stopwords -->
        <div class="iwptp-editor-row-option">
            <label>
                Stopwords
                <small>
                    These are generic words to be excluded during search to conserve server resource and increase result accuracy. They will be included during full keyword phrase search.<br>
                    Comma separate the stopwords.
                </small>
            </label>
            <textarea iwptp-model-key="" disabled></textarea>
        </div>

        <!-- replacements -->
        <div class="iwptp-editor-row-option">
            <label>
                Replacements
                <small>
                    Correct common spelling mistakes and smartly replace keywords to increase result accuracy. Will not affect full keyword phrase search. <br>
                    Enter one correction per line in this format: Correction: Incorrect 1 | Incorrect 2 ...
                </small>
            </label>
            <textarea iwptp-model-key="" disabled></textarea>
        </div>

        <!-- override global settings -->
        <div class="iwptp-editor-row-option" iwptp-model-key="">

            <label>
                Search page override settings
                <small>Note: First enable search page override from Archive Override facility</small>
                <small>Note: Use [iwptp_search] in a text widget to provide search bar with category selector</small>
            </label>

            <div class="iwptp-editor-row-option">
                <label>Select target fields to search through:</label>
                <!-- target -->
                <?php foreach (array('Title', 'Content', 'Excerpt', 'SKU', 'Custom field', 'Category', 'Attribute', 'Tag') as $field) : ?>
                    <?php $model_val = strtolower(str_replace(' ', '_', $field)); ?>
                    <?php
                    if (in_array($field, array('Title', 'Content'))) {
                    ?>
                        <label>
                            <input type="checkbox" value="" iwptp-model-key="" disabled />
                            <?php echo esc_html($field); ?>
                        </label>
                    <?php
                    } else {
                        iwptp_checkbox($model_val, $field, "", 'disabled');
                    }
                    ?>

                    <?php if ($model_val === 'custom_field') : ?>
                        <div class="iwptp-checkbox-selection-group" iwptp-panel-condition="prop" iwptp-condition-prop="target" iwptp-condition-val="custom_field">
                            <label>
                                <small>Select custom fields to search through:</small>
                            </label>
                            <?php foreach (iwptp_get_product_custom_fields() as $meta_name) : ?>
                                <label class="iwptp-editor-checkbox-label">
                                    <input type="checkbox" iwptp-model-key="" value="" disabled />
                                    <?php echo esc_html($meta_name); ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($model_val === 'attribute') : ?>
                        <div class="iwptp-checkbox-selection-group" iwptp-panel-condition="prop" iwptp-condition-prop="target" iwptp-condition-val="attribute">
                            <label>
                                <small>Select attributes to search through:</small>
                            </label>
                            <?php foreach (wc_get_attribute_taxonomies() as $attribute) : ?>
                                <label class="iwptp-editor-checkbox-label">
                                    <input type="checkbox" iwptp-model-key="" value="" disabled />
                                    <?php echo esc_html($attribute->attribute_label); ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                <?php endforeach; ?>
            </div>
        </div>

        <!-- weightage rules -->
        <div class="iwptp-editor-row-option">
            <label>
                Weightage rules
                <small>Assign relative weights for keyword matches to different product properties</small>
            </label>
        </div>

        <?php
        foreach (array('title', 'sku', 'category', 'attribute', 'tag', 'content', 'excerpt', 'custom_field') as $field) {
            $heading = str_replace(array('-', '_'), ' ', ucfirst($field));
            if ($field === 'sku') {
                $heading = 'SKU';
            }

            if (in_array($field, array('attribute', 'custom_field'))) {
                require 'search__common1.php';
            } else {
                require 'search__common2.php';
            }
        }
        ?>
    </div>
</div>