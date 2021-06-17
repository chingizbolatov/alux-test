<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrencyPairs extends Model
{
    protected $table = 'currency_pairs';

    protected $fillable = [
        'c1_id',
        'c2_id',
        'price',
        'history'
    ];
}
