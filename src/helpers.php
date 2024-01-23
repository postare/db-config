<?php

if (! function_exists('setting')) {
    function setting(string $key): mixed
    {
        return \Postare\DbConfig\DbConfig::get($key);
    }
}
