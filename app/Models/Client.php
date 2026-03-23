<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'client_service')
            ->withTimestamps();
    }

    public function clientServices(): HasMany
    {
        return $this->hasMany(ClientService::class);
    }
}