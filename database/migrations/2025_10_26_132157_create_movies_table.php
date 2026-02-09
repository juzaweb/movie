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
        Schema::create('movies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('origin_name')->nullable();
            $table->decimal('rating', 3, 1)->nullable();
            $table->date('release')->nullable();
            $table->year('year')->nullable();
            $table->string('runtime')->nullable();
            $table->string('video_quality')->nullable();
            $table->string('trailer_link')->nullable();
            $table->integer('current_episode')->nullable();
            $table->integer('max_episode')->nullable();
            $table->string('status', 50)->default('draft');
            $table->bigInteger('views')->default(0);
            $table->boolean('is_tv_series')->default(false);
            $table->float('tmdb_rating')->nullable();
            $table->datetimes();
        });

        Schema::create(
            'movie_translations',
            function (Blueprint $table) {
                $table->id();
                $table->uuid('movie_id')->index();
                $table->string('locale', 5)->index();
                $table->string('name');
                $table->text('description')->nullable();
                $table->text('content')->nullable();
                $table->string('slug', 190)->index();
                $table->unique(['movie_id', 'locale']);
                $table->unique(['slug']);

                $table->foreign('movie_id')
                    ->references('id')
                    ->on('movies')
                    ->onDelete('cascade');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movie_translations');
        Schema::dropIfExists('movies');
    }
};
