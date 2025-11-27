<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'email',
        'ip_address',
        'user_agent',
        'successful',
        'error_message',
        'created_at',
    ];

    protected $casts = [
        'successful' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }
}
