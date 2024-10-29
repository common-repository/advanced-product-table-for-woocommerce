<div class="iwptp-option-row">
    <div class="iwptp-option-label">
        <?php esc_html_e("Instant search", 'ithemeland-woocommerce-product-table-pro-lite'); ?><span class="iwptp-pro-badge" title="For Pro Version">Pro</span>
        <i class="dashicons dashicons-info" title="<?php echo "[it_product_table id='123' instant_search='true'] <br>
        Overrides the search navigation filter to perform client side filtering. <br> Useful when you are displaying only one page of results in the table.<br>
        Please note limitation:<br>
        1. Only searches in products that are already loaded on the browser page.<br>
        2. Only searches through the text that is printed in the table product rows.<br>
        3. Ignores all search match weightage rules. Performs simple search.<br>
        4. Can be slow in case of large number of products on page.<br>
        5. This facility will not work with fixed columns or heading"; ?>"></i>
    </div>
    <div class="iwptp-input">
        <label>
            <input class="iwptp-instant-search" iwptp-model-key="" type="checkbox" value="on" disabled>
            <?php esc_html_e('Enable', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
    </div>
</div>

<div class="iwptp-option-row">
    <div class="iwptp-option-label">
        <?php esc_html_e("Open dropdown on click", 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        <i class="dashicons dashicons-info" title="<?php echo "[it_product_table id='123' open_dropdown_on_click='true']<br>
        This makes navigation filter dropdowns open only when they are <br> clicked instead of opening on mouse hover."; ?>"></i>
    </div>
    <div class="iwptp-input">
        <label>
            <input class="iwptp-open-dropdown-on-click" iwptp-model-key="open_dropdown_on_click" type="checkbox" value="on">
            <?php esc_html_e('Enable', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
    </div>
</div>

<div class="iwptp-option-row">
    <div class="iwptp-option-label">
        <?php esc_html_e("Show for user roles", 'ithemeland-woocommerce-product-table-pro-lite'); ?><span class="iwptp-pro-badge" title="For Pro Version">Pro</span>
        <i class="dashicons dashicons-info" title="<?php echo "[it_product_table id='123' show_for_user_role='guest | administrator | shop_manager'] <br>
        Enter user role slugs separated by pound ' | '."; ?>"></i>
    </div>
    <div class="iwptp-input">
        <select name="" id="" class="" iwptp-model-key="" data-placeholder="Select roles" disabled>
            <option><?php esc_html_e('Select', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
        </select>
    </div>
</div>

<div class="iwptp-option-row">
    <div class="iwptp-option-label">
        <?php esc_html_e("Hide for user roles", 'ithemeland-woocommerce-product-table-pro-lite'); ?><span class="iwptp-pro-badge" title="For Pro Version">Pro</span>
        <i class="dashicons dashicons-info" title="<?php echo "[it_product_table id='123' hide_for_user_role='guest | administrator | shop_manager'] <br>
        Enter user role slugs separated by pound ' | '."; ?>"></i>
    </div>
    <div class="iwptp-input">
        <select class="" iwptp-model-key="" data-placeholder="Select roles" disabled>
            <option><?php esc_html_e('Select', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
        </select>
    </div>
</div>

<div class="iwptp-option-row">
    <div class="iwptp-option-label">
        <?php esc_html_e("Refresh table", 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        <i class="dashicons dashicons-info" title="<?php echo "[it_product_table id='123' refresh_table='true'] <br>
        This will lock up the table whenever an add to cart / remove from <br>
        cart action takes place and refresh its view when the action is completed. <br>
        The purpose is to refresh prices and stocks to keep them always in line <br>
        with any special pricing rule applied via custom code or 3rd party plugin. "; ?>"></i>
    </div>
    <div class="iwptp-input">
        <label>
            <input class="iwptp-refresh-table" iwptp-model-key="refresh_table" type="checkbox" value="on">
            <?php esc_html_e('Enable', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
    </div>
</div>

<div class="iwptp-option-row">
    <div class="iwptp-option-label">
        <?php esc_html_e("laptop auto scroll", 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        <i class="dashicons dashicons-info" title="<?php echo "[it_product_table id='123' laptop_auto_scroll='true'] <br>
        When you are using this, each time your site visitor refreshes <br>
         the table over AJAX (by filtering, changing table page, searching or sorting) <br> 
         IWPTPL will automatically scroll the page to bring the top of the table <br> 
         container into view for the convenience of the site visitor."; ?>"></i>
    </div>
    <div class="iwptp-input">
        <label>
            <input class="iwptp-refresh-table" iwptp-model-key="laptop_auto_scroll" type="checkbox" value="on">
            <?php esc_html_e('Enable', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
    </div>
</div>

<div class="iwptp-option-row">
    <div class="iwptp-option-label">
        <?php esc_html_e("Tablet auto scroll", 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        <i class="dashicons dashicons-info" title="<?php echo "[it_product_table id='123' tablet_auto_scroll='true'] <br>
        When you are using this, each time your site visitor refreshes <br>
         the table over AJAX (by filtering, changing table page, searching or sorting) <br> 
         IWPTPL will automatically scroll the page to bring the top of the table <br> 
         container into view for the convenience of the site visitor."; ?>"></i>
    </div>
    <div class="iwptp-input">
        <label>
            <input class="iwptp-refresh-table" iwptp-model-key="tablet_auto_scroll" type="checkbox" value="on">
            <?php esc_html_e('Enable', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
    </div>
</div>

<div class="iwptp-option-row">
    <div class="iwptp-option-label">
        <?php esc_html_e("Phone auto scroll", 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        <i class="dashicons dashicons-info" title="<?php echo "[it_product_table id='123' phone_auto_scroll='true'] <br>
        When you are using this, each time your site visitor refreshes <br>
         the table over AJAX (by filtering, changing table page, searching or sorting) <br> 
         IWPTPL will automatically scroll the page to bring the top of the table <br> 
         container into view for the convenience of the site visitor."; ?>"></i>
    </div>
    <div class="iwptp-input">
        <label>
            <input class="iwptp-refresh-table" iwptp-model-key="phone_auto_scroll" type="checkbox" value="on">
            <?php esc_html_e('Enable', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
    </div>
</div>

<div class="iwptp-option-row">
    <div class="iwptp-option-label">
        <?php esc_html_e("Laptop freeze", 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        <i class="dashicons dashicons-info" title="<?php echo "[it_product_table id='123' laptop_freeze_left='1' laptop_freeze_right='1'] <br>
        Use this facility to freeze columns on the left and right of your table while <br>
        allowing it to scroll horizontally. Similar to what you can do in an Excel file. <br>
        Note: <br>
        Please keep in mind this facility is highly resource intensive <br>
        on the client device (visitor's browsing device). Therefore avoid using <br> 
        it on tables with a very large number of products, and conduct adequate <br>
        performance testing. <br>
        You can also use grab_and_scroll='true' to enable horizontal scrolling <br>
        by holding the mouse down on the table and scrolling it left and right. "; ?>"></i>
    </div>
    <div class="iwptp-input">
        <label style="display: inline-block; margin-right: 5px;">
            <?php esc_html_e('Left:', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            <input class="iwptp-laptop-freeze-left" iwptp-model-key="laptop_freeze_left" type="number" value="">
        </label>
        <label style="display: inline-block; margin-right: 5px;">
            <?php esc_html_e('Right:', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            <input class="iwptp-laptop-freeze-right" iwptp-model-key="laptop_freeze_right" type="number" value="">
        </label>
    </div>
</div>

<div class="iwptp-option-row">
    <div class="iwptp-option-label">
        <?php esc_html_e("Laptop freeze heading", 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        <i class="dashicons dashicons-info" title="<?php echo "[it_product_table id='123' laptop_freeze_heading='true'] <br>
        You can freeze the table heading at the top of the screen using this facility,<br>
        making it convenient for the site visitor to keep track of the columns on large tables.<br>
        If another element on your site is already fixed at the top of the screen, <br>
        like a mega menu, you might find the fixed table heading getting hidden behind <br>
        it. In such a case you can shift the fixed table heading further<br>
        down by using the laptop_scroll_offset facility that is covered above."; ?>"></i>
    </div>
    <div class="iwptp-input">
        <label>
            <input class="iwptp-laptop-freeze-heading" iwptp-model-key="laptop_freeze_heading" type="checkbox" value="">
            <?php esc_html_e('Enable', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
    </div>
</div>

<div class="iwptp-option-row">
    <div class="iwptp-option-label">
        <?php esc_html_e("Tablet freeze", 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        <i class="dashicons dashicons-info" title="<?php echo "[it_product_table id='123' tablet_freeze_left='1' tablet_freeze_right='1'] <br>
        Use this facility to freeze columns on the left and right of your table while <br>
        allowing it to scroll horizontally. Similar to what you can do in an Excel file. <br>
        Note: <br>
        Please keep in mind this facility is highly resource intensive <br>
        on the client device (visitor's browsing device). Therefore avoid using <br> 
        it on tables with a very large number of products, and conduct adequate <br>
        performance testing. <br>
        You can also use grab_and_scroll='true' to enable horizontal scrolling <br>
        by holding the mouse down on the table and scrolling it left and right. "; ?>"></i>
    </div>
    <div class="iwptp-input">
        <label style="display: inline-block; margin-right: 5px;">
            <?php esc_html_e('Left:', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            <input class="iwptp-tablet-freeze-left" iwptp-model-key="tablet_freeze_left" type="number" value="">
        </label>
        <label style="display: inline-block; margin-right: 5px;">
            <?php esc_html_e('Right:', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            <input class="iwptp-tablet-freeze-right" iwptp-model-key="tablet_freeze_right" type="number" value="">
        </label>
    </div>
</div>

<div class="iwptp-option-row">
    <div class="iwptp-option-label">
        <?php esc_html_e("Tablet freeze heading", 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        <i class="dashicons dashicons-info" title="<?php echo "[it_product_table id='123' tablet_freeze_heading='true'] <br>
        You can freeze the table heading at the top of the screen using this facility,<br>
        making it convenient for the site visitor to keep track of the columns on large tables.<br>
        If another element on your site is already fixed at the top of the screen, <br>
        like a mega menu, you might find the fixed table heading getting hidden behind <br>
        it. In such a case you can shift the fixed table heading further<br>
        down by using the laptop_scroll_offset facility that is covered above."; ?>"></i>
    </div>
    <div class="iwptp-input">
        <label>
            <input class="iwptp-tablet-freeze-heading" iwptp-model-key="tablet_freeze_heading" type="checkbox" value="">
            <?php esc_html_e('Enable', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
    </div>
</div>

<div class="iwptp-option-row">
    <div class="iwptp-option-label">
        <?php esc_html_e("Phone freeze", 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        <i class="dashicons dashicons-info" title="<?php echo "[it_product_table id='123' phone_freeze_left='1' phone_freeze_right='1'] <br>
        Use this facility to freeze columns on the left and right of your table while <br>
        allowing it to scroll horizontally. Similar to what you can do in an Excel file. <br>
        Note: <br>
        Please keep in mind this facility is highly resource intensive <br>
        on the client device (visitor's browsing device). Therefore avoid using <br> 
        it on tables with a very large number of products, and conduct adequate <br>
        performance testing. <br>
        You can also use grab_and_scroll='true' to enable horizontal scrolling <br>
        by holding the mouse down on the table and scrolling it left and right. "; ?>"></i>
    </div>
    <div class="iwptp-input">
        <label style="display: inline-block; margin-right: 5px;">
            <?php esc_html_e('Left:', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            <input class="iwptp-phone-freeze-left" iwptp-model-key="phone_freeze_left" type="number" value="">
        </label>
        <label style="display: inline-block; margin-right: 5px;">
            <?php esc_html_e('Right:', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            <input class="iwptp-phone-freeze-right" iwptp-model-key="phone_freeze_right" type="number" value="">
        </label>
    </div>
</div>

<div class="iwptp-option-row">
    <div class="iwptp-option-label">
        <?php esc_html_e("Phone freeze heading", 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        <i class="dashicons dashicons-info" title="<?php echo "[it_product_table id='123' phone_freeze_heading='true'] <br>
        You can freeze the table heading at the top of the screen using this facility,<br>
        making it convenient for the site visitor to keep track of the columns on large tables.<br>
        If another element on your site is already fixed at the top of the screen, <br>
        like a mega menu, you might find the fixed table heading getting hidden behind <br>
        it. In such a case you can shift the fixed table heading further<br>
        down by using the laptop_scroll_offset facility that is covered above."; ?>"></i>
    </div>
    <div class="iwptp-input">
        <label>
            <input class="iwptp-phone-freeze-heading" iwptp-model-key="phone_freeze_heading" type="checkbox" value="">
            <?php esc_html_e('Enable', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
    </div>
</div>

<div class="iwptp-option-row">
    <div class="iwptp-option-label">
        <?php esc_html_e("Lazy load table", 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        <i class="dashicons dashicons-info" title="<?php echo "[it_product_table id='123' lazy_load='true'] <br> 
        Speed up page load by lazy loading the table."; ?>"></i>
    </div>
    <div class="iwptp-input">
        <label>
            <input class="iwptp-lazy-load-table" iwptp-model-key="lazy_load_table" type="checkbox" value="on">
            <?php esc_html_e('Enable', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
    </div>
</div>

<div class="iwptp-option-row">
    <div class="iwptp-option-label">
        <?php esc_html_e("Hide empty columns", 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        <i class="dashicons dashicons-info" title="<?php echo "[it_product_table id='123' hide_empty_columns='true'] <br>
        Use this to hide columns that have no output in them. <br>
        Please note, that this will only work if none of the cells <br> 
        in a column is printing anything. <br>
        If you have attribute or custom field elements in the column that are <br>
        configured to show a default output when there is no value, <br>
        then the column will still be shown even if there is no <br>
        attribute term or custom field to print from the element."; ?>"></i>
    </div>
    <div class="iwptp-input">
        <label>
            <input class="iwptp-hide-empty-columns" iwptp-model-key="hide_empty_columns" type="checkbox" value="on">
            <?php esc_html_e('Enable', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
    </div>
</div>

<div class="iwptp-option-row">
    <div class="iwptp-option-label">
        <?php esc_html_e("Hide empty table", 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        <i class="dashicons dashicons-info" title="<?php echo "[it_product_table id='123' hide_empty_table='true'] <br>
        If the table has no results when it is first loaded, <br>
        it will be hidden from view. This helps avoid unnecessarily <br>
        showing completely blank tables on the screen. <br>
        This does not affect tables that show no results after <br>
        customer has used the navigation filters. "; ?>"></i>
    </div>
    <div class="iwptp-input">
        <label>
            <input class="iwptp-hide-empty-table" iwptp-model-key="hide_empty_table" type="checkbox" value="on">
            <?php esc_html_e('Enable', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
    </div>
</div>

<div class="iwptp-option-row">
    <div class="iwptp-option-label">
        <?php esc_html_e("Show variation on one row", 'ithemeland-woocommerce-product-table-pro-lite'); ?><span class="iwptp-pro-badge" title="For Pro Version">Pro</span>
        <i class="dashicons dashicons-info" title="<?php echo "[it_product_table id='123' product_variations='true'] <br>
        Please see docs. Lets you display the product variations of <br> 
        a specific variable product in a table in separate rows."; ?>"></i>
    </div>
    <div class="iwptp-input">
        <label>
            <input class="iwptp-show-variation-on-one-row" iwptp-model-key="" type="checkbox" value="on" disabled>
            <?php esc_html_e('Enable', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
    </div>
</div>

<div class="iwptp-option-row">
    <div class="iwptp-option-label">
        <?php esc_html_e("Quick Buy for Multi add to cart", 'ithemeland-woocommerce-product-table-pro-lite'); ?><span class="iwptp-pro-badge" title="For Pro Version">Pro</span>
        <i class="dashicons dashicons-info" title="<?php echo "[it_product_table id='123' quick_buy_for_multi_add_to_cart='true'] <br>
        Redirect to the Cart/Checkout page after successful addition."; ?>"></i>
    </div>
    <div class="iwptp-input">
        <select class="iwptp-quick-buy-for-multi-add-to-cart" iwptp-model-key="" disabled>
            <option value="default"><?php esc_html_e('Default', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
        </select>
    </div>
</div>

<div class="iwptp-option-row">
    <div class="iwptp-option-label">
        <?php esc_html_e("Quick Buy for each product", 'ithemeland-woocommerce-product-table-pro-lite'); ?><span class="iwptp-pro-badge" title="For Pro Version">Pro</span>
        <i class="dashicons dashicons-info" title="<?php echo "[it_product_table id='123' quick_buy_for_each_product='true'] <br>
        Redirect to the Cart/Checkout page after successful addition."; ?>"></i>
    </div>
    <div class="iwptp-input">
        <select class="iwptp-quick-buy-for-each-product" iwptp-model-key="" disabled>
            <option value="default"><?php esc_html_e('Default', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
        </select>
    </div>
</div>

<div class="iwptp-option-row">
    <div class="iwptp-option-label">
        <?php esc_html_e("Disable ajax", 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        <i class="dashicons dashicons-info" title="<?php echo "[it_product_table id='123' disable_ajax='true'] <br>
        This can be useful when 3rd party plugin elements are being <br>
        displayed inside IWPTPL and require full page reload upon filtering <br>
        and pagination to show up correctly."; ?>"></i>
    </div>
    <div class="iwptp-input">
        <label>
            <input class="iwptp-disable-ajax" iwptp-model-key="disable_ajax" type="checkbox" value="on">
            <?php esc_html_e('Enable', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
    </div>
</div>

<div class="iwptp-option-row">
    <div class="iwptp-option-label">
        <?php esc_html_e("Disable url update", 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        <i class="dashicons dashicons-info" title="<?php echo "[it_product_table id='123' disable_url_update='true'] <br>
        Prevents the browser url from continuously changing <br> 
        when the IWPTPL navigation filters are used."; ?>"></i>
    </div>
    <div class="iwptp-input">
        <label>
            <input class="iwptp-disable-url-update" iwptp-model-key="disable_url_update" type="checkbox" value="on">
            <?php esc_html_e('Enable', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
    </div>
</div>

<div class="iwptp-option-row">
    <div class="iwptp-option-label">
        <?php esc_html_e("Loading", 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        <i class="dashicons dashicons-info" title="<?php esc_attr_e("Loading", 'ithemeland-woocommerce-product-table-pro-lite'); ?>"></i>
    </div>
    <div class="iwptp-input">
        <label style="display: inline-block; margin-right: 5px; width: 32%;">
            <?php esc_html_e('Overlay Background', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            <input class="iwptp-color-picker iwptp-loading-color-picker" iwptp-model-key="loading_background_color" type="text" value="">
        </label>
        <label style="display: inline-block; width: 32%;">
            <?php esc_html_e('Image Size', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            <input class="iwptp-loading-image-size" iwptp-model-key="loading_image_size" type="number" value="">
        </label>
        <label style="display: inline-block; margin-right: 5px; width: 32%;">
            <button class="iwptp-button iwptp-button-blue iwptp-media-image-button" data-type="single" data-target=".iwptp-editor-settings-loading-icon" iwptp-model-key="loading_background_image" type="button" value=""><?php esc_html_e("Choose Icon", 'ithemeland-woocommerce-product-table-pro-lite'); ?></button>
            <div class="iwptp-editor-settings-loading-icon" data-type="media_image_target">
                <input type="hidden" class="iwptp-editor-settings-loading-image-id" iwptp-model-key="loading_image_id" data-type="image_id" value="">
                <input type="hidden" class="iwptp-editor-settings-loading-image-url" iwptp-model-key="loading_image_url" data-type="image_url" value="">
                <div class="iwptp-editor-settings-media-loading-preview" data-type="preview"></div>
            </div>
        </label>
    </div>
</div>