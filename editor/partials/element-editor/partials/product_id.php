<div class="iwptp-element-settings-content-item active" data-content="general">
    <div class="iwptp-editor-row-option">
        <?php iwptp_checkbox('true', 'Switch ID based on selected variation', 'variable_switch'); ?>
    </div>

    <?php include('html-class.php'); ?>

    <?php include('condition/outer.php'); ?>
</div>

<div class="iwptp-element-settings-content-item" data-content="style" iwptp-model-key="style">
    <?php include('style/common.php'); ?>
</div>