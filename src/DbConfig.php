<?php

namespace Postare\DbConfig;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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
        [$group, $setting] = static::parseKey($key);

        $cachename = "db-config.{$group}.{$setting}";

        $default = $default ?? "[{$group}.{$setting}]";

        $data = Cache::rememberForever($cachename, fn () => static::fetchConfig($group, $setting));

        $value = data_get($data, $setting, $default);

        return $value ?? $default;
    }

    /**
     * Set a configuration value in the database.
     *
     * @param  string  $key  The configuration key.
     * @param  mixed  $value  The configuration value.
     */
    public static function set(string $key, mixed $value): void
    {
        [$group, $setting] = static::parseKey($key);

        $cachename = "db-config.{$group}.{$setting}";

        Cache::forget($cachename);

        static::storeConfig($group, $setting, $value);
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

        DB::table('db_config')->where('group', $group)->get()->each(function ($setting) use (&$settings) {
            $settings[$setting->key] = json_decode($setting->settings, true);
        });

        return $settings;
    }

    /**
     * Parses a given key and returns an array containing the group and setting.
     *
     * @param  string  $key  The key to be parsed.
     * @return array An array containing the group and setting.
     */
    protected static function parseKey(string $key): array
    {
        $keyParts = explode('.', $key);
        $group = array_shift($keyParts);
        $setting = $keyParts[0] ?? null;

        return [$group, $setting];
    }

    protected static function fetchConfig(string $group, string $setting): array
    {
        $item = DB::table('db_config')
            ->where('group', $group)
            ->where('key', $setting)
            ->first();

        return [
            $setting => json_decode($item->settings, true),
        ];
    }

    protected static function storeConfig(string $group, string $setting, mixed $value): void
    {
        DB::table('db_config')
            ->updateOrInsert(
                [
                    'group' => $group,
                    'key' => $setting,
                ],
                [
                    'settings' => json_encode($value),
                ]
            );
    }
}
