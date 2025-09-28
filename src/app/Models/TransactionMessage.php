<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'sender_id',
        'message',
        'image_path',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function reads()
    {
        // Relationship placeholder for message reads
        // Replace with TransactionMessageRead::class if you add a model
        return $this->hasMany(TransactionMessageRead::class, 'message_id');
    }
}
