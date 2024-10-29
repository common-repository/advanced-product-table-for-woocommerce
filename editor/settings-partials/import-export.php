<style>
    .iwptp-import-export-wrapper {
        width: 100%;
        display: inline-block;
        padding: 30px;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        -webkit-box-sizing: border-box;
    }

    .iwptp-import-export-button {
        padding: 14px 20px;
        border-radius: 4px;
        display: inline-block;
        font-size: 18px;
        margin-right: 10px;
        color: #fff;
        transition: all 0.2s;
        -moz-transition: all 0.2s;
        -webkit-transition: all 0.2s;
    }

    .iwptp-import-button {
        background: #009c6e;
        border: 1px solid #068860;
    }

    .iwptp-export-button {
        background: #00ac56;
        border: 1px solid #008844;
    }

    .iwptp-import-button:hover {
        background: #068860;
    }

    .iwptp-export-button:hover {
        background: #008844;
    }

    .iwptp-import-button:hover,
    .iwptp-export-button:hover {
        transition: all 0.2s;
        -moz-transition: all 0.2s;
        -webkit-transition: all 0.2s;
    }

    .iwptp-import-icon svg,
    .iwptp-export-icon svg {
        height: .9em;
        stroke-width: 2.5px;
        vertical-align: baseline;
        position: relative;
        top: 1px;
    }

    .iwptp-import-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, .5);
        z-index: 10000;
    }

    .iwptp-import-modal>form {
        background: white;
        width: 300px;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        padding: 40px;
        font-size: 16px;
        line-height: 1.5em;
        border-radius: 5px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
    }

    .iwptp-import-modal>form:after {
        content: '';
        position: absolute;
        left: 10px;
        top: 10px;
        border: 2px solid #f7f7f7;
        width: calc(100% - 20px);
        height: calc(100% - 20px);
        border-radius: inherit;
        box-sizing: border-box;
        pointer-events: none;
    }

    .iwptp-import-modal>form>h2 {
        font-size: 20px;
        font-weight: bold;
        margin: .25em 0 1em;
    }

    .iwptp-import-modal>form>ol {
        padding-left: 1em;
        margin: 1em 0;
    }

    .iwptp-show-import-modal>.iwptp-import-modal {
        display: block;
    }

    .iwptp-show-import-modal input[type="file"] {
        margin: 4px 0 8px;
        border: 2px solid rgba(0, 0, 0, 0.04);
        border-width: 2px 0px;
        padding: 16px 0;
    }

    .iwptp-show-import-modal input[type="submit"] {
        margin-top: 14px;
        font-size: 16px;
        padding: 10px 25px;
        color: #000;
    }

    .iwptp-pro .iwptp-import-export-button {
        cursor: pointer;
        box-shadow: 1px 1px 1px rgba(0, 0, 0, .04);
    }

    .iwptp-import-export-button .iwptp-pro-badge {
        border-radius: 3px;
        font-size: 12px;
        background: #EF5350;
        color: white;
        padding: 4px 8px;
        margin-left: .75em;
        vertical-align: middle;
    }
</style>

<?php
if (empty($iwptp_import_export_button_label_append)) {
    $iwptp_import_export_button_label_append = 'settings';
}

if (empty($iwptp_import_export_button_context)) {
    $iwptp_import_export_button_context = 'settings';
}
?>
<div class="iwptp-import-export-wrapper ">
    <span class="iwptp-import-button iwptp-import-export-button">
        <?php echo wp_kses(iwptp_icon('download', 'iwptp-import-icon'), iwptp_allowed_html_tags()); ?>
        Import
        <?php echo esc_html($iwptp_import_export_button_label_append); ?>
    </span>
    <span class="iwptp-export-button iwptp-import-export-button">
        <?php echo wp_kses(iwptp_icon('upload', 'iwptp-export-icon'), iwptp_allowed_html_tags()); ?>
        Export
        <?php echo esc_html($iwptp_import_export_button_label_append); ?>
    </span>
    <div class="iwptp-import-modal">
        <form method="POST" enctype="multipart/form-data">

            <h2>Import <?php echo esc_html($iwptp_import_export_button_label_append); ?></h2>

            <span>During demo import, please follow:</span>
            <ol>
                <li>Backup site database</li>
                <li>Import WooCommerce products</li>
                <li>Import the product tables</li>
                <li>Import the table settings</li>
            </ol>

            <input type="file" name="iwptp_import_file">
            <br>
            <input type="submit" class="iwptp-import-export-button" />

            <input type="hidden" name="iwptp_import_export_nonce" value="<?php echo esc_attr(wp_create_nonce('iwptp_import_export')); ?>" />
            <input type="hidden" name="iwptp_context" value="<?php echo esc_attr($iwptp_import_export_button_context); ?>" />
            <input type="hidden" name="iwptp_action" />
            <input type="hidden" name="iwptp_export_id" value="" />
        </form>
    </div>
</div>

<script>
    (function($) {
        $(function() {

            // import
            $('body').on('click', '.iwptp-import-button', function() {
                var $this = $(this),
                    $wrapper = $this.parent();
                $wrapper.addClass('iwptp-show-import-modal');
                $('input[name="iwptp_action"]', $wrapper).val('import');
            })

            $('body').on('click', '.iwptp-import-modal', function(e) {
                if (e.target === this) {
                    var cl = 'iwptp-show-import-modal';
                    $(this).closest('.' + cl).removeClass(cl);
                }
            })

            // export
            $('body').on('click', '.iwptp-export-button', function() {
                var $this = $(this),
                    $wrapper = $this.parent();
                $('input[name="iwptp_action"]', $wrapper).val('export');
                $wrapper.find('form').submit();
            })
        })
    })(jQuery)
</script>