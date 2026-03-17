<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'name',
        'company',
        'phone',
        'email',
        'address',
        'tax_id',
        'whatsapp',
        'website_url',
        'social_links',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'social_links' => 'array',
    ];

    // public function projects(): HasMany
    // {
    //     return $this->hasMany(Project::class);
    // }
}