<?php

namespace App\Helpers;

class InvoiceHelper
{
    /**
     * Generate unique invoice number.
     */
    public static function generate(): string
    {
        $prefix = config('app_constants.invoice.prefix', 'INV-');

        return $prefix . time();
    }

    /**
     * Format invoice number for display.
     */
    public static function format(string $invoiceNumber): string
    {
        return strtoupper($invoiceNumber);
    }
}
