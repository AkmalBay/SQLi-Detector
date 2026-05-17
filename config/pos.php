<?php

return [

    /*
    |--------------------------------------------------------------------------
    | POS General Settings
    |--------------------------------------------------------------------------
    */

    'cart' => [
        'max_items' => 100,
        'max_quantity_per_item' => 9999,
        'hold_cart_retention_hours' => 24,
    ],

    /*
    |--------------------------------------------------------------------------
    | Search Settings
    |--------------------------------------------------------------------------
    */

    'search' => [
        'product_limit' => 10,
        'min_keyword_length' => 2,
    ],

    /*
    |--------------------------------------------------------------------------
    | Receipt Settings
    |--------------------------------------------------------------------------
    */

    'receipt' => [
        'width' => 58, // mm (thermal printer width)
        'show_customer_info' => true,
        'show_cashier_info' => true,
        'show_footer' => true,
        'footer_message' => 'Terima kasih telah berbelanja di toko kami!',
    ],

    /*
    |--------------------------------------------------------------------------
    | Shift Settings
    |--------------------------------------------------------------------------
    */

    'shift' => [
        'require_shift_for_transaction' => true,
        'auto_close_after_hours' => null, // Set to number of hours or null to disable
    ],

    /*
    |--------------------------------------------------------------------------
    | Display Settings
    |--------------------------------------------------------------------------
    */

    'display' => [
        'show_low_stock_warning' => true,
        'low_stock_threshold' => 5,
        'show_product_image' => true,
    ],

];
