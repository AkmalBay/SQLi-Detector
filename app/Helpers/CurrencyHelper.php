<?php

namespace App\Helpers;

class CurrencyHelper
{
    /**
     * Format amount to Indonesian Rupiah.
     */
    public static function formatRupiah($amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    /**
     * Parse formatted currency string back to float.
     */
    public static function parseRupiah(string $formatted): float
    {
        // Remove "Rp " and dots, then replace with nothing
        $cleaned = str_replace(['Rp ', '.'], '', $formatted);
        return (float) $cleaned;
    }

    /**
     * Format amount for display in forms/inputs.
     */
    public static function formatInput($amount): string
    {
        return number_format($amount, 0, ',', '.');
    }
}
