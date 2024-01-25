<?php

namespace Postare\DbConfig;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DbConfig
{
    /**
     * Recupera un'impostazione dal database.
     *
     * @param  string  $key  Chiave dell'impostazione, può includere la notazione "puntata" per sottoelementi.
     *                       esempio: "mortage_rate.anticipo_percentuale" $durataAnniPredefinita = \App\Helpers\SettingsHelper::get('mortage_rate.durata_anni_predefinita');
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $parts = explode('.', $key);
        $key = array_shift($parts);

        // Utilizzo del caching per evitare chiamate al database multiple
        $data = Cache::remember("db-config.$key", 3600, function () use ($key) {
            $setting = DB::table('db_config')->where('key', $key)->first();

            return $setting ? json_decode($setting->settings, true) : [];
        });

        $subKey = implode('.', $parts);

        return data_get($data, $subKey, $default);
    }

    /**
     * Imposta o aggiorna il valore di un'impostazione nel database.
     * Se l'impostazione non esiste, verrà creata.
     *
     * @param  string  $key  Chiave dell'impostazione, può includere la notazione "puntata" per sottoelementi.
     * @param  mixed  $value  Valore da impostare per la chiave specificata.
     */
    /**
     * Imposta o aggiorna il valore di un'impostazione nel database.
     * Se l'impostazione non esiste, verrà creata.
     *
     * @param  string  $key  Chiave dell'impostazione, può includere la notazione "puntata" per sottoelementi.
     * @param  mixed  $value  Valore da impostare per la chiave specificata.
     */
    public static function set(string $key, mixed $value): void
    {
        // Divide la chiave per determinare il nome dell'impostazione e il percorso delle sottochiavi
        $parts = explode('.', $key);
        $key = array_shift($parts);

        DB::transaction(function () use ($key, $parts, $value) {
            // Tenta di ottenere l'impostazione corrente
            $setting = DB::table('db_config')->where('key', $key)->lockForUpdate()->first();

            $settings = $setting ? json_decode($setting->settings, true) : [];

            // Se ci sono sotto-chiavi, aggiornale nel JSON, altrimenti aggiorna l'intero valore
            if ($parts) {
                data_set($settings, implode('.', $parts), $value);
            } else {
                $settings = $value;
            }

            // Se l'impostazione esiste, aggiorna, altrimenti crea una nuova riga
            if ($setting) {
                DB::table('db_config')->where('key', $key)->update(['preferences' => json_encode($settings)]);
            } else {
                DB::table('db_config')->insert([
                    'key' => $key,
                    'settings' => json_encode($settings),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        // Invalida la cache relativa all'impostazione
        Cache::forget("settings.$key");
    }
}
