<?php

namespace Postare\DbSettings\Commands;

use Illuminate\Console\Command;

class DbSettingsCommand extends Command
{
    public $signature = 'db-settings';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
