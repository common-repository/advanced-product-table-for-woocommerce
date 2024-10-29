<div class="iwptp-editor-row-option iwptp-search-rules__custom-rules" iwptp-model-key="">

    <span class="iwptp-search-rules__match">Match</span>
    <span class="iwptp-search-rules__score">Score</span>

    <!-- phrase exact -->
    <div class="iwptp-editor-row-option">
        <label>
            <input type="checkbox" iwptp-model-key="" disabled>
            <span class="iwptp-search-rules__match-name">Phrase exact</span>
            <span class="iwptp-search-rules__match-description">$term === "$keyword_phrase"</span>
            <input type="number" min="0" iwptp-model-key="" disabled>
        </label>
    </div>

    <!-- phrase like -->
    <div class="iwptp-editor-row-option">
        <label>
            <input type="checkbox" iwptp-model-key="" disabled>
            <span class="iwptp-search-rules__match-name">Phrase like</span>
            <span class="iwptp-search-rules__match-description">$term = $word "...{$keyword_phrase}..." $word</span>
            <input type="number" min="0" iwptp-model-key="" disabled>
        </label>
    </div>

    <!-- keyword exact -->
    <div class="iwptp-editor-row-option">
        <label>
            <input type="checkbox" iwptp-model-key="" disabled>
            <span class="iwptp-search-rules__match-name">Keyword exact</span>
            <span class="iwptp-search-rules__match-description">$term = $word "$keyword" $word</span>
            <input type="number" min="0" iwptp-model-key="" disabled>
        </label>
    </div>

    <!-- keyword like -->
    <div class="iwptp-editor-row-option">
        <label>
            <input type="checkbox" iwptp-model-key="" disabled>
            <span class="iwptp-search-rules__match-name">Keyword like</span>
            <span class="iwptp-search-rules__match-description">$term = $word "...{$keyword}..." $word</span>
            <input type="number" min="0" iwptp-model-key="" disabled>
        </label>
    </div>

</div>