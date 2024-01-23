<?php

if (! function_exists('setting')) {
    function setting(string $key): mixed
    {
        return \Postare\DbSettings\DbSettings::get($key);
    }
}
