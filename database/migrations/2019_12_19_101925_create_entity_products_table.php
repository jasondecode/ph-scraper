<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntityProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entity_products', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->mediumText('name');

            $table->integer('votes');

            $table->date('featured_at');

            $table->mediumText('topics');

            $table->mediumText('shortened_url');

            $table->mediumText('slug');

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
        Schema::dropIfExists('entity_products');
    }
}
