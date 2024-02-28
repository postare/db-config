<?php
namespace Postare\DbConfig\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Postare\DbConfig\Models\Config;

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

?>
