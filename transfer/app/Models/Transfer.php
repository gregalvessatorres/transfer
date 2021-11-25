<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{

    protected $fillable = [
        'value',
        'payer',
        'payee'
    ];

    public function payer()
    {
        return $this->belongsTo(User::class, 'payer', 'id');
    }

    public function payee()
    {
        return $this->belongsTo(User::class, 'payee', 'id');
    }
}