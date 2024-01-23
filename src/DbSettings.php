<?php

namespace Postare\DbSettings;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DbSettings
{
    /**
     * Recupera un'impostazione dal database.
     *
     * @param  string  $key  Chiave dell'impostazione, può includere la notazione "puntata" per sottoelementi.
     *                       esempio: "mortage_rate.anticipo_percentuale" $durataAnniPredefinita = \App\Helpers\SettingsHelper::get('mortage_rate.durata_anni_predefinita');
     */
    public static function get(string $key): mixed
    {
        $parts = explode('.', $key);
        $name = array_shift($parts);

        // Utilizzo del caching per evitare chiamate al database multiple
        $data = Cache::remember("settings.$name", 3600, function () use ($name) {
            $setting = DB::table('settings')->where('name', $name)->first();

            return $setting ? json_decode($setting->preferences, true) : [];
        });

        $subKey = implode('.', $parts);

        return data_get($data, $subKey);
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
        $name = array_shift($parts);

        DB::transaction(function () use ($name, $parts, $value) {
            // Tenta di ottenere l'impostazione corrente
            $setting = DB::table('settings')->where('name', $name)->lockForUpdate()->first();

            $preferences = $setting ? json_decode($setting->preferences, true) : [];

            // Se ci sono sotto-chiavi, aggiornale nel JSON, altrimenti aggiorna l'intero valore
            if ($parts) {
                data_set($preferences, implode('.', $parts), $value);
            } else {
                $preferences = $value;
            }

            // Se l'impostazione esiste, aggiorna, altrimenti crea una nuova riga
            if ($setting) {
                DB::table('settings')->where('name', $name)->update(['preferences' => json_encode($preferences)]);
            } else {
                DB::table('settings')->insert([
                    'name' => $name,
                    'preferences' => json_encode($preferences),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        // Invalida la cache relativa all'impostazione
        Cache::forget("settings.$name");
    }
}
