<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogEntriesScrapersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_entries_scrapers', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->boolean('is_running')->default(false);

            $table->longText('error')->nullable();

            $table->text('source');            

            $table->dateTime('runned_at')->nullable()->default(null);

            $table->dateTime('completed_at')->nullable()->default(null);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_entries_scrapers');
    }
}
