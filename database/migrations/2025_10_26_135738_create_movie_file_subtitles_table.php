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
        Schema::create(
            'server_file_subtitles',
            function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('label');
                $table->string('url');
                $table->string('language', 10)->nullable();
                $table->integer('display_order')->default(0);
                $table->boolean('active')->default(true);
                $table->uuid('file_id')->index();
                $table->datetimes();

                $table->foreign('file_id')
                    ->references('id')
                    ->on('server_files')
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
        Schema::dropIfExists('server_file_subtitles');
    }
};
