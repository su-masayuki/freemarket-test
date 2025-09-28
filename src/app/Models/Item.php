<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['name','brand_name', 'price', 'description', 'image_path', 'condition', 'is_sold', 'likes_count', 'user_id'];

    public function purchasers()
    {
        return $this->belongsToMany(User::class, 'purchases');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'likes')->withTimestamps();
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_item', 'item_id', 'category_id');
    }
    public function messages()
    {
        return $this->hasMany(TransactionMessage::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
