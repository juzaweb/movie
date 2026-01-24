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
        Schema::create('movie_years', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug', 190)->index();
            $table->datetimes();

            $table->unique(['slug']);
        });

        Schema::create('movie_movie_year', function (Blueprint $table) {
            $table->uuid('movie_id');
            $table->uuid('movie_year_id');
            $table->primary(['movie_id', 'movie_year_id']);

            $table->foreign('movie_id')
                ->references('id')
                ->on('movies')
                ->onDelete('cascade');

            $table->foreign('movie_year_id')
                ->references('id')
                ->on('movie_years')
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
        Schema::dropIfExists('movie_movie_year');
        Schema::dropIfExists('movie_years');
    }
};
