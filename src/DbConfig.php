<?php

namespace Postare\DbConfig;

use Illuminate\Support\Facades\Cache;
use Postare\DbConfig\Models\Config;

class DbConfig
{
    /**
     * Retrieve a configuration value from the database.
     *
     * @param  string  $key  The configuration key.
     * @param  mixed  $default  The default value to return if the configuration key is not found.
     * @return mixed The configuration value.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $keyParts = explode('.', $key);
        $group = array_shift($keyParts); // Prende il primo elemento dell'array e lo rimuove dall'array
        $setting = $keyParts[0] ?? null;

        $cachename = "db-config.{$group}.{$setting}";

        // Utilizzo del caching per evitare chiamate al database multiple
        $data = Cache::rememberForever($cachename, function () use ($group, $setting) {

            $item = Config::where('group', $group)->where('key', $setting)->first();

            return [$setting => $item->settings ?? null];

        });

        $subKey = implode('.', $keyParts);

        return data_get($data, $subKey, $default);

    }

    /**
     * Set a configuration value in the database.
     *
     * @param  string  $key  The configuration key.
     * @param  mixed  $value  The configuration value.
     */
    public static function set(string $key, mixed $value): void
    {
        $keyParts = explode('.', $key);
        $group = array_shift($keyParts);
        $setting = $keyParts[0] ?? null;

        $cachename = "db-config.{$group}.{$setting}";

        $config = Config::firstOrNew([
            'group' => $group,
            'key' => $setting,
        ]);

        $config->settings = $value;

        if ($config->isDirty()) {
            Cache::forget($cachename);
            $config->save();
        }
    }

    /**
     * Retrieves the settings for a specific group from the database.
     *
     * @param  string  $group  The group name.
     * @return array|null The settings for the group, or null if no settings are found.
     */
    public static function getGroup(string $group): ?array
    {
        $settings = [];

        Config::where('group', $group)->get()->each(function ($setting) use (&$settings) {
            $settings[$setting->key] = $setting->settings;
        });

        return $settings;
    }
}
