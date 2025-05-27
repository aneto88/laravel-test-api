<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FavoriteProduct extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'product_id',
        'title',
        'price',
        'image',
        'review'
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
