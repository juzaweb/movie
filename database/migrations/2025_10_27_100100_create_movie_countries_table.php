<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movie_countries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->datetimes();
        });

        Schema::create(
            'movie_country_translations',
            function (Blueprint $table) {
                $table->id();
                $table->uuid('movie_country_id')->index();
                $table->string('locale', 5)->index();
                $table->string('name');
                $table->string('slug', 190)->index();
                $table->unique(['movie_country_id', 'locale']);
                $table->unique(['slug']);

                $table->foreign('movie_country_id')
                    ->references('id')
                    ->on('movie_countries')
                    ->onDelete('cascade');
            }
        );

        Schema::create('movie_movie_country', function (Blueprint $table) {
            $table->uuid('movie_id');
            $table->uuid('movie_country_id');
            $table->primary(['movie_id', 'movie_country_id']);

            $table->foreign('movie_id')
                ->references('id')
                ->on('movies')
                ->onDelete('cascade');

            $table->foreign('movie_country_id')
                ->references('id')
                ->on('movie_countries')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movie_movie_country');
        Schema::dropIfExists('movie_country_translations');
        Schema::dropIfExists('movie_countries');
    }
};
