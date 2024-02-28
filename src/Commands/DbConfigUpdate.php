<?php

namespace Postare\DbConfig\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Postare\DbConfig\DbConfig;

class DbConfigUpdate extends Command
{
    public $signature = 'db_config:update';

    public $description = 'Create a new settings';

    /**
     * Filesystem instance
     */
    protected Filesystem $files;

    /**
     * Create a new command instance.
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // Add field to database table
        $this->info('Updating database table');

        // Check if table exists and has the field group
        if (Schema::hasTable('db_config') && ! Schema::hasColumn('db_config', 'group')) {
            $this->info('Adding field group to db_config table');
            Schema::table('db_config', function ($table) {
                $table->string('group')->after('id');
                $table->unique(['group', 'key']);
            });
        } else {
            $this->info('table db_config already has field group');
        }

        $groups = DB::table('db_config')
            ->where('group', '')
            ->get();

        if ($groups->count() > 0) {
            $this->info('Updating group field in db_config table');

            foreach ($groups as $group) {

                $groupName = $group->key;

                $settings = json_decode($group->settings, true);

                foreach ($settings as $key => $setting) {
                    DbConfig::set("$groupName.$key", $setting);
                }

            }

            DB::table('db_config')
                ->where('group', '')
                ->delete();

        } else {
            $this->info('No records to update');
        }

    }
}
