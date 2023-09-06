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
        Schema::table('users', function (Blueprint $table) {

            $table->foreignId('apartment_id')->constrained();
        });

        Schema::table('apartment_service', function (Blueprint $table) {

            $table->foreignId('apartment_id')->constrained();
            $table->foreignId('service_id')->constrained();
        });


        Schema::table('apartment_sponsors', function (Blueprint $table) {

            $table->foreignId('apartment_id')->constrained();
            $table->foreignId('sponsor_id')->constrained();
        });

        Schema::table('messages', function (Blueprint $table) {

            $table->foreignId('apartment_id')->constrained();

        });

        Schema::table('views', function (Blueprint $table) {

            $table->foreignId('apartment_id')->constrained();

        });

        Schema::table('images', function (Blueprint $table) {

            $table->foreignId('apartment_id')->constrained();

        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropForeign('users_apartment_id_foreign');

            $table->dropColumn('apartment_id');
        });

        
        Schema::table('apartment_service', function (Blueprint $table) {

            $table->dropForeign('apartment_service_apartment_id_foreign');
            $table->dropForeign('apartment_service_service_id_foreign');

            $table->dropColumn('apartment_id');
            $table->dropColumn('service_id');
        });


        Schema::table('apartment_sponsors', function (Blueprint $table) {

            $table->dropForeign('apartment_sponsors_apartment_id_foreign');
            $table->dropForeign('apartment_sponsors_sponsor_id_foreign');

            $table->dropColumn('apartment_id');
            $table->dropColumn('sponsor_id');
        });

        Schema::table('messages', function (Blueprint $table) {

            $table->dropForeign('messages_apartments_id_foreign');

            $table->dropColumn('apartment_id');

        });

        Schema::table('views', function (Blueprint $table) {

            $table->dropForeign('views_apartments_id_foreign');

            $table->dropColumn('apartment_id');

        });

        // Tabella immagini
        Schema::table('images', function (Blueprint $table) {

            $table->dropForeign('images_apartments_id_foreign');

            $table->dropColumn('apartment_id');


        });
    }
};
