<?php

namespace Postare\DbConfig;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DbConfig
{


    /**
     * Retrieve a configuration value from the database.
     *
     * @param string $key The configuration key.
     * @param mixed $default The default value to return if the configuration key is not found.
     * @return mixed The configuration value.
     */
    public static function get(string $key, mixed $default = null): mixed
    {

        $cachename = "db-config.$key";

        $keyParts = explode('.', $key);
        $group = array_shift($keyParts); // Prende il primo elemento dell'array e lo rimuove dall'array
        $setting = $keyParts[0] ?? null;

        // Utilizzo del caching per evitare chiamate al database multiple
        $data = Cache::rememberForever($cachename, function () use ($group, $setting) {

            $item = DB::table('db_config')
            ->where('group', $group)
            ->where('key', $setting)
            ->first();

            return [
                $setting => json_decode($item->settings, true)
            ];

        });

        $subKey = implode('.', $keyParts);

        return data_get($data, $subKey, $default);

    }

    /**
     * Set a configuration value in the database.
     *
     * @param string $key The configuration key.
     * @param mixed $value The configuration value.
     * @return void
     */
    public static function set(string $key, mixed $value): void
    {
        $keyParts = explode('.', $key);
        $group = array_shift($keyParts);
        $setting = $keyParts[0] ?? null;

        $data = Cache::forget("db-config.$key");

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

    /**
     * Retrieves the settings for a specific group from the database.
     *
     * @param string $group The group name.
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

}
