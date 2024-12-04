<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['user_id', 'products'];

    protected $casts = [
        'products' => 'array',  
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
