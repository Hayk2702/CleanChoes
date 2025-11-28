<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPhotoPathToReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reviews', function (Blueprint $table) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->string('title')->nullable()->after('rate');
                $table->string('photo_path')->nullable()->after('rate');
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reviews', function (Blueprint $table) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->dropColumn('title');
                $table->dropColumn('photo_path');
            });
        });
    }
}
