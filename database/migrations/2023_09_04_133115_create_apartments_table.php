<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apartments', function (Blueprint $table) {
            $table->id();

            $table->string('name', 128);
            $table->text('description', 255);
            $table->integer('room');
            $table->integer('bathroom');
            $table->integer('mq');
            $table->text('address');
            $table->float('latitude')->hidden();
            $table->float('longitude')->hidden();
            $table->boolean('visible')->default(true);
            $table->boolean('sponsor')->default(false);
            $table->text('cover');


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
        Schema::dropIfExists('apartments');
    }
};
