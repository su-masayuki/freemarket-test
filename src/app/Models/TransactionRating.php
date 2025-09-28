<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'rater_id',
        'ratee_id',
        'rating',
        'comment',
    ];
}
