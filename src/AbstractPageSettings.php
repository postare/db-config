<?php

namespace Postare\DbConfig;

use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

abstract class AbstractPageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    protected static ?string $navigationGroup = 'Impostazioni';

    // Metodo astratto per ottenere il nome delle impostazioni specifiche
    abstract protected function settingName(): string;

    public function mount(): void
    {
        if (! DB::table('db_config')->where('key', $this->settingName())->exists()) {
            DB::table('db_config')->insert([
                'key' => $this->settingName(),
                'settings' => json_encode([]),
            ]);
        }

        $setting = DB::table('db_config')->where('key', $this->settingName())->first()->settings;
        $setting = json_decode($setting, true);

        $this->form->fill($setting);
    }

    public function save(): void
    {

        try {
            $data = $this->form->getState();

            DB::table('db_config')
                ->where('key', $this->settingName())
                ->update([
                    'settings' => $data,
                ]);

        } catch (Halt $exception) {
            return;
        }

        Cache::forget("db-config.{$this->settingName()}");

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
