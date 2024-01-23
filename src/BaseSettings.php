<?php

namespace Postare\DbSettings;

use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

abstract class BaseSettings extends Page implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    protected static ?string $navigationGroup = 'Impostazioni';

    // Metodo astratto per ottenere il nome delle impostazioni specifiche
    abstract protected function settingName(): string;

    public function mount(): void
    {
        // if $this->settingName() doesn't exist in the settings table, create it
        if (! DB::table('settings')->where('key', $this->settingName())->exists()) {
            DB::table('settings')->insert([
                'key' => $this->settingName(),
                'settings' => json_encode([]),
            ]);
        }

        $setting = DB::table('settings')->where('key', $this->settingName())->first()->settings;
        $setting = json_decode($setting, true);

        $this->form->fill($setting);
    }

    public function save(): void
    {

        try {
            $data = $this->form->getState();

            DB::table('settings')
                ->where('key', $this->settingName())
                ->update([
                    'settings' => $data,
                ]);

        } catch (Halt $exception) {
            return;
        }

        Cache::forget("settings.{$this->settingName()}");

        Notification::make()
            ->success()
            ->title(__('filament-panels::resources/pages/edit-record.notifications.saved.title'))
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->submit('save'),
        ];
    }
}
