<?php

namespace Postare\DbConfig\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Postare\DbConfig
 *\DbConfig
 */
class DbConfig extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Postare\DbConfig\DbConfig::class;
    }
}
