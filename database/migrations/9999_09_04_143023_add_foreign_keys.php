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

        Schema::table('apartments', function (Blueprint $table) {

            $table->foreignId('image_id')->constrained();
            $table->foreignId('view_id')->constrained();
            $table->foreignId('message_id')->constrained();
        });


        Schema::table('apartment_service', function (Blueprint $table) {

            $table->foreignId('apartment_id')->constrained();
            $table->foreignId('service_id')->constrained();
        });


        Schema::table('apartment_sponsors', function (Blueprint $table) {

            $table->foreignId('apartment_id')->constrained();
            $table->foreignId('sponsor_id')->constrained();
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

        Schema::table('apartments', function (Blueprint $table) {

            $table->dropForeign('apartments_message_id_foreign');

            $table->dropColumn('message_id');

            $table->dropForeign('apartments_view_id_foreign');

            $table->dropColumn('view_id');

            $table->dropForeign('apartments_image_id_foreign');

            $table->dropColumn('image_id');
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
    }
};
