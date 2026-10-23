<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = [
        'service_type',
        'name',
        'email',
        'phone',
        'details',
        'is_spam',
        'spam_reason',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'is_spam' => 'boolean',
    ];
}
