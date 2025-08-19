<?php

if (! function_exists('db_config')) {
    /**
     * Retrieve a configuration value from the database.
     *
     * @param  string  $key  The configuration key.
     * @param  mixed  $default  The default value to return if the configuration key is not found.
     * @return mixed The configuration value.
     */
    function db_config(string $key, mixed $default = null): mixed
    {
        return \Postare\DbConfig\DbConfig::get($key, $default);
    }
}
