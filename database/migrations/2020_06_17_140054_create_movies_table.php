<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMoviesTable extends Migration
{
    public function up()
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 250);
            $table->string('origin_title', 250)->nullable();
            $table->string('thumbnail', 150)->nullable();
            $table->string('poster', 150)->nullable();
            $table->string('slug', 150)->unique()->index();
            $table->string('description', 300)->nullable();
            $table->longText('content')->nullable();
            $table->string('rating', 25)->nullable();
            $table->date('release')->nullable();
            $table->integer('year')->nullable();
            $table->string('runtime', 100)->nullable();
            $table->string('video_quality', 100)->nullable();
            $table->string('trailer_link', 100)->nullable();
            $table->integer('current_episode')->nullable();
            $table->integer('max_episode')->nullable();
            $table->tinyInteger('tv_series')->default(0);
            $table->tinyInteger('is_paid')->default(0);
            $table->string('status', 50)->default('draft');
            $table->bigInteger('views')->default(0);
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('movies');
    }
}
