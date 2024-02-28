<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('db_config', function (Blueprint $table) {
            $table->id();

            $table->string('group');

            $table->string('key');

            $table->json('settings')->nullable();

            $table->unique(['group', 'key']);

            $table->timestamps();
        });
    }
};
