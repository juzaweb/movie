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
        Schema::create('live_tvs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('streaming_url');
            $table->bigInteger('views')->default(0);
            $table->datetimes();
        });

        Schema::create(
            'live_tv_translations',
            function (Blueprint $table) {
                $table->id();
                $table->uuid('live_tv_id')->index();
                $table->string('locale', 5)->index();
                $table->string('name');
                $table->text('content')->nullable();
                $table->text('description')->nullable();
                $table->string('slug', 190)->index();
                $table->unique(['live_tv_id', 'locale']);
                $table->unique(['slug']);

                $table->foreign('live_tv_id')
                    ->references('id')
                    ->on('live_tvs')
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
        Schema::dropIfExists('live_tv_translations');
        Schema::dropIfExists('live_tvs');
    }
};
