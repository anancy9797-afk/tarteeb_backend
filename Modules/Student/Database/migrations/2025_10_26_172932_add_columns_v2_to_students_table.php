<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->text('distinguished_skills')->nullable()->after('other_notes');
            $table->boolean('has_previous_education')->default(0)->after('distinguished_skills');
            $table->text('previous_school_data')->nullable()->after('has_previous_education');
            $table->boolean('can_distinguish_letters_randomly')->nullable()->default(0)->after('previous_school_data');
            $table->enum('reads_short_words', ['excellent', 'very_good', 'good', 'cannot_read'])->nullable()->after('can_distinguish_letters_randomly');
            $table->enum('reads_short_sentences', ['excellent', 'very_good', 'good', 'cannot_read'])->nullable()->after('reads_short_words');
            $table->boolean('memorizes_quran_surahs')->nullable()->default(0)->after('reads_short_sentences');
            $table->string('memorizes_quran_from')->nullable()->after('memorizes_quran_surahs');
            $table->string('memorizes_quran_to')->nullable()->after('memorizes_quran_from');
            $table->text('additional_educational_notes')->nullable()->after('memorizes_quran_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {

        });
    }
};
