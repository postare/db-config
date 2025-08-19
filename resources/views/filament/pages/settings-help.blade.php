<x-filament::section>
    <x-slot name="heading">
        <p class="font-semibold">{{ __('db-config::db-config.page_created') }}</p>
    </x-slot>
    <x-slot name="description">
        <p>{!! __('db-config::db-config.page_description', ['group' => $group]) !!}</p>
    </x-slot>
    <h2 class="fi-header-subheading">{{ __('db-config::db-config.next_steps') }}</h2>
    <ul>
        <li>1. {!! __('db-config::db-config.step_add_fields', [
            'file' => "<code>app/Filament/Pages/{$pageClass}.php</code>",
            'example' => "<code>\\Filament\\Forms\\Components\\TextInput::make('site_name')</code>",
        ]) !!}
        </li>
        <li>
            2. {!! __('db-config::db-config.step_retrieve_values', ['group' => $group]) !!}
        </li>
    </ul>
</x-filament::section>
