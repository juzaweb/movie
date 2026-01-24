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
        Schema::create('report_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamps();
        });

        Schema::create('report_types_translations', function (Blueprint $table) {
            $table->id();
            $table->uuid('report_type_id');
            $table->string('locale');
            $table->string('name');
            $table->timestamps();

            $table->unique(['report_type_id', 'locale']);
            $table->foreign('report_type_id')
                ->references('id')
                ->on('report_types')
                ->onDelete('cascade');
        });

        Schema::create('reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('report_type_id');
            $table->uuid('reportable_id')->nullable();
            $table->string('reportable_type')->nullable();
            $table->json('meta')->nullable()->comment('E.x: {"video_id": "123"}');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'processed'])->default('pending');
            $table->creator();
            $table->timestamps();

            $table->foreign('report_type_id')->references('id')->on('report_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reports');
        Schema::dropIfExists('report_types_translations');
        Schema::dropIfExists('report_types');
    }
};
