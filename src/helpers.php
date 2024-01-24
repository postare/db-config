<?php

if (! function_exists('db_config')) {
    function db_config(string $key, mixed $default = null): mixed
    {
        return \Postare\DbConfig\DbConfig::get($key, $default);
    }
}
