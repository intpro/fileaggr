<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration
{
    /**
     * @return void
     */
    public function up()
    {
        Schema::create('files', function(Blueprint $table)
        {
            $table->increments('id');

            $table->string('name');

            $table->string('entity_name');
            $table->integer('entity_id');

            $table->string('link');
            $table->string('title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('files');
    }
}
