<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'username',
        'name',
        // 'email',
        'password',
        'division_id',
        'role',
        'status',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    // ];

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    // Add relationship to password reset requests
    public function password_reset_requests()
    {
        return $this->hasMany(PasswordResetRequest::class);
    }

    // Manually check the user's role
    public function isAdmin()
    {
        return $this->role === 'Admin';  // Check if the role is 'Admin'
    }
}