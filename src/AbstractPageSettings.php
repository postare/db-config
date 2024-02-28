<?php

namespace Postare\DbConfig;

use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

abstract class AbstractPageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    protected static ?string $navigationGroup = 'Impostazioni';

    // Metodo astratto per ottenere il nome delle impostazioni specifiche
    abstract protected function settingName(): string;

    public function mount(): void
    {
        $this->data = DbConfig::getGroup($this->settingName());
        $this->form->fill($this->data);
    }

    public function save(): void
    {
        collect($this->form->getState())->each(function ($setting, $key) {
            DbConfig::set($this->settingName() . '.' . $key, $setting);
        });

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
