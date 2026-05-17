<?php

namespace App\Exceptions;

use Exception;

class TransactionFailedException extends Exception
{
    protected $errors;

    public function __construct(
        string $message = 'Transaksi gagal diproses.',
        array $errors = []
    ) {
        parent::__construct($message);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function render()
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
            'error' => 'transaction_failed',
            'errors' => $this->errors,
        ], 422);
    }
}
