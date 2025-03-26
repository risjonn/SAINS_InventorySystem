<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProductThreshold extends Model
{
    protected $fillable = [
        'user_id',
        'category',
        'low_threshold',
        'critical_threshold',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}