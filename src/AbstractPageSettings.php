<?php

namespace Postare\DbConfig;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

abstract class AbstractPageSettings extends Page
{
    use InteractsWithActions;

    public ?array $data = [];

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    public static function getNavigationGroup(): ?string
    {
        return __('db-config::db-config.navigation_group');
    }

    abstract protected function settingName(): string;

    public function mount(): void
    {
        $this->data = DbConfig::getGroup($this->settingName());
        $this->content->fill($this->data);
    }

    public function save(): void
    {
        collect($this->content->getState())->each(function ($setting, $key) {
            DbConfig::set($this->settingName().'.'.$key, $setting);
        });

        Notification::make()
            ->success()
            ->title(__('db-config::db-config.saved_title'))
            ->body(__('db-config::db-config.saved_body'))
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label(__('db-config::db-config.save'))
                ->action(fn () => $this->save()),
        ];
    }
}
