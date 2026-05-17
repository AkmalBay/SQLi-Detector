<?php

namespace App\Exceptions;

use Exception;

class ShiftNotOpenException extends Exception
{
    public function __construct(
        string $message = 'Shift belum dibuka. Silakan buka shift terlebih dahulu.'
    ) {
        parent::__construct($message);
    }

    public function render()
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
            'error' => 'shift_not_open',
        ], 403);
    }
}
