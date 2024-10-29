<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <title><?php esc_html_e('Cell Template Editor', 'ithemeland-woocommerce-product-table-pro-lite'); ?></title>
</head>

<body>
    <?php include('tabs.php'); ?>
    <script type="text/javascript">
        jQuery(function($) {
            $('.iwptp-tabs').iwptp_tabs()
                .on('tab_enabled tab_disabled', function(e, index) {});
        })
    </script>
</body>

</html>