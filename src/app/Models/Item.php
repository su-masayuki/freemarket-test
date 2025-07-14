<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['name','brand_name', 'price', 'description', 'image_path', 'condition', 'is_sold', 'likes_count', 'user_id', 'category'];

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
}
