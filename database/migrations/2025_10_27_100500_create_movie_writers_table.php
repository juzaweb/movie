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
        Schema::create('movie_writers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->datetimes();
        });

        Schema::create(
            'movie_writer_translations',
            function (Blueprint $table) {
                $table->id();
                $table->uuid('movie_writer_id')->index();
                $table->string('locale', 5)->index();
                $table->string('name');
                $table->string('slug', 190)->index();
                $table->text('bio')->nullable();
                $table->unique(['movie_writer_id', 'locale']);
                $table->unique(['slug']);

                $table->foreign('movie_writer_id')
                    ->references('id')
                    ->on('movie_writers')
                    ->onDelete('cascade');
            }
        );

        Schema::create('movie_movie_writer', function (Blueprint $table) {
            $table->uuid('movie_id');
            $table->uuid('movie_writer_id');
            $table->primary(['movie_id', 'movie_writer_id']);

            $table->foreign('movie_id')
                ->references('id')
                ->on('movies')
                ->onDelete('cascade');

            $table->foreign('movie_writer_id')
                ->references('id')
                ->on('movie_writers')
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
        Schema::dropIfExists('movie_movie_writer');
        Schema::dropIfExists('movie_writer_translations');
        Schema::dropIfExists('movie_writers');
    }
};
