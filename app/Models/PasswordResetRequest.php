<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordResetRequest extends Model
{
    protected $fillable = [
        'user_id',
        'completed',
        'type',
        'status_viewed',
    ];

    /**
     * The user who requested the password reset.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
