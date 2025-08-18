<?php

namespace Postare\DbConfig;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use UnitEnum;

abstract class AbstractPageSettings extends Page
{
    use InteractsWithActions;

    public ?array $data = [];

    protected static UnitEnum | string | null $navigationGroup = 'Impostazioni';

    // Metodo astratto per ottenere il nome delle impostazioni specifiche
    abstract protected function settingName(): string;

    public function mount(): void
    {
        $this->data = DbConfig::getGroup($this->settingName());
        $this->content->fill($this->data);
    }

    public function save(): void
    {
        collect($this->content->getState())->each(function ($setting, $key) {
            DbConfig::set($this->settingName() . '.' . $key, $setting);
        });

        Notification::make()
            ->success()
            ->title(__('Salvato'))
            ->body(__('Le impostazioni sono state salvate con successo.'))
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label(__('Salva'))
                ->action(fn () => $this->save()),
        ];
    }
}
