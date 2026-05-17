<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionPayment extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'transaction_id',
        'payment_method',
        'amount_paid',
        'reference_number',
        'pos_shift_id',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
    
    public function posShift()
    {
        return $this->belongsTo(PosShift::class);
    }
}