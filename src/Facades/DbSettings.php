<?php

namespace Postare\DbSettings\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Postare\DbSettings\DbSettings
 */
class DbSettings extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Postare\DbSettings\DbSettings::class;
    }
}
