<div class="shortcode-generator-results">
    <div class="shortcode-error"></div>

    <div class="shortcode-preview">
        <label class="preview-title">Preview</label>
        <div class="preview-container">
            <label>Your live preview will appear here</label>
        </div>
    </div>

    <label class="preview-title">Form Shortcode</label>
    <textarea class="result-shortcode have-slim-scrollbar" placeholder="Your generated shortcode will appear here"></textarea>

    <div class="copy-button-container">
        <button class="copy-button have-copy-indicator" onclick="event.preventDefault(); locationOverrideGenerator.copyShortcode();">
            copy
            <span class="copy-button-icon">🗊</span>
        </button>
    </div>

    <label class="preview-title">Form CSS</label>
    <textarea class="result-css" placeholder="Your generated CSS Style will appear here"></textarea>
    <div class="copy-button-container">
        <button class="copy-button have-copy-indicator" onclick="event.preventDefault(); locationOverrideGenerator.copyStyle();">
            copy
            <span class="copy-button-icon">🗊</span>
        </button>
    </div>
    
    <button class="copy-all-button have-copy-indicator button-primary" alt="copy" onclick="
        event.preventDefault()
        locationOverrideGenerator.copyAll()
    "><span>🗊</span>Copy All</button>

    <p class="instructions">
        Paste the shortcode anywhere on your site to display location override form.
        <a class="instructions-link" target="_blank" href="https://www.if-so.com/dynamic-select-form/manual-user-location-selection/">
            Learn more &gt;
        </a>
    </p>
</div>

<script>
    document.querySelectorAll('.have-copy-indicator').forEach(el => {
        el.addEventListener('click', function() {
            el.classList.add('active')
            setTimeout(function() { el.classList.remove('active') } , 1000)
        })
    })
</script>
