<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartHold extends Model
{
    protected $guarded = ['id'];

    // Otomatis ubah JSON di database jadi Array di PHP
    protected $casts = [
        'items' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}