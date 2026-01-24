<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add name column to main tables
        Schema::table('movie_actors', function (Blueprint $table) {
            $table->string('name')->nullable();
        });

        Schema::table('movie_directors', function (Blueprint $table) {
            $table->string('name')->nullable();
        });

        Schema::table('movie_writers', function (Blueprint $table) {
            $table->string('name')->nullable();
        });

        // Copy name from translation tables to main tables
        // For actors
        DB::statement("
            UPDATE movie_actors ma
            INNER JOIN (
                SELECT movie_actor_id, name
                FROM movie_actor_translations
                WHERE locale = (
                    SELECT locale
                    FROM movie_actor_translations mat2
                    WHERE mat2.movie_actor_id = movie_actor_translations.movie_actor_id
                    ORDER BY locale ASC
                    LIMIT 1
                )
            ) mat ON ma.id = mat.movie_actor_id
            SET ma.name = mat.name
        ");

        // For directors
        DB::statement("
            UPDATE movie_directors md
            INNER JOIN (
                SELECT movie_director_id, name
                FROM movie_director_translations
                WHERE locale = (
                    SELECT locale
                    FROM movie_director_translations mdt2
                    WHERE mdt2.movie_director_id = movie_director_translations.movie_director_id
                    ORDER BY locale ASC
                    LIMIT 1
                )
            ) mdt ON md.id = mdt.movie_director_id
            SET md.name = mdt.name
        ");

        // For writers
        DB::statement("
            UPDATE movie_writers mw
            INNER JOIN (
                SELECT movie_writer_id, name
                FROM movie_writer_translations
                WHERE locale = (
                    SELECT locale
                    FROM movie_writer_translations mwt2
                    WHERE mwt2.movie_writer_id = movie_writer_translations.movie_writer_id
                    ORDER BY locale ASC
                    LIMIT 1
                )
            ) mwt ON mw.id = mwt.movie_writer_id
            SET mw.name = mwt.name
        ");

        // Make name column NOT NULL after data migration
        Schema::table('movie_actors', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
        });

        Schema::table('movie_directors', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
        });

        Schema::table('movie_writers', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
        });

        // Drop name column from translation tables
        Schema::table('movie_actor_translations', function (Blueprint $table) {
            $table->dropColumn('name');
        });

        Schema::table('movie_director_translations', function (Blueprint $table) {
            $table->dropColumn('name');
        });

        Schema::table('movie_writer_translations', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Add name column back to translation tables
        Schema::table('movie_actor_translations', function (Blueprint $table) {
            $table->string('name')->after('locale');
        });

        Schema::table('movie_director_translations', function (Blueprint $table) {
            $table->string('name')->after('locale');
        });

        Schema::table('movie_writer_translations', function (Blueprint $table) {
            $table->string('name')->after('locale');
        });

        // Copy name back to translation tables
        DB::statement("
            UPDATE movie_actor_translations mat
            INNER JOIN movie_actors ma ON mat.movie_actor_id = ma.id
            SET mat.name = ma.name
        ");

        DB::statement("
            UPDATE movie_director_translations mdt
            INNER JOIN movie_directors md ON mdt.movie_director_id = md.id
            SET mdt.name = md.name
        ");

        DB::statement("
            UPDATE movie_writer_translations mwt
            INNER JOIN movie_writers mw ON mwt.movie_writer_id = mw.id
            SET mwt.name = mw.name
        ");

        // Drop name column from main tables
        Schema::table('movie_actors', function (Blueprint $table) {
            $table->dropColumn('name');
        });

        Schema::table('movie_directors', function (Blueprint $table) {
            $table->dropColumn('name');
        });

        Schema::table('movie_writers', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }
};
