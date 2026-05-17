<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pagination Settings
    |--------------------------------------------------------------------------
    |
    | Default pagination size for various sections of the application.
    |
    */

    'pagination' => [
        'products' => 10,
        'transactions' => 15,
        'inventory_logs' => 20,
        'suppliers' => 10,
        'categories' => 10,
    ],

    /*
    |--------------------------------------------------------------------------
    | Invoice Settings
    |--------------------------------------------------------------------------
    |
    | Invoice number prefixes and formats.
    |
    */

    'invoice' => [
        'prefix' => 'INV-',
        'length' => 12, // Total length of invoice number
    ],

    /*
    |--------------------------------------------------------------------------
    | Inventory Settings
    |--------------------------------------------------------------------------
    |
    | Default values for inventory operations.
    |
    */

    'inventory' => [
        'low_stock_threshold' => 10,
        'initial_stock_description' => 'Stok Awal (Produk Baru)',
        'restock_description_prefix' => 'Restock',
        'stock_out_description_prefix' => 'Keluar',
    ],

    /*
    |--------------------------------------------------------------------------
    | Customer Settings
    |--------------------------------------------------------------------------
    |
    | Default customer naming pattern for POS.
    |
    */

    'customer' => [
        'default_name_pattern' => 'Pelanggan {time}', // {time} will be replaced with H:i
    ],

    /*
    |--------------------------------------------------------------------------
    | Shift Settings
    |--------------------------------------------------------------------------
    |
    | POS shift related settings.
    |
    */

    'shift' => [
        'max_starting_cash' => 999999999,
        'max_notes_length' => 1000,
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Methods
    |--------------------------------------------------------------------------
    |
    | Available payment methods for transactions.
    |
    */

    'payment_methods' => [
        'CASH' => 'Tunai',
        'DEBIT' => 'Kartu Debit',
        'QRIS' => 'QRIS',
        'E_WALLET' => 'E-Wallet',
    ],

    /*
    |--------------------------------------------------------------------------
    | Inventory Log Types
    |--------------------------------------------------------------------------
    |
    | Types of inventory movements.
    |
    */

    'inventory_types' => [
        'in' => 'Masuk',
        'out' => 'Keluar',
        'opname' => 'Stock Opname',
    ],

];
