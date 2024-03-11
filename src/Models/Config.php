<?php

namespace Postare\DbConfig\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $table = 'db_config';

    protected $fillable = [
        'group',
        'key',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];
}
