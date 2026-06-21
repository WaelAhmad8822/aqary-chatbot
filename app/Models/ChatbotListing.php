<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ChatbotListing extends Model
{
    protected $table = 'properties';

    protected $casts = [
        'price' => 'integer',
        'area_sqm' => 'integer',
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
        'is_furnished' => 'boolean',
        'features' => 'array',
        'images' => 'array',
        'timeslots' => 'array',
    ];
}
