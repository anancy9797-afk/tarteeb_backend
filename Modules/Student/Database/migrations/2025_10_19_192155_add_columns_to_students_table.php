<?php

use Modules\Country\App\Models\State;
use Illuminate\Support\Facades\Schema;
use Modules\Country\App\Models\Branch;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->foreignIdFor(State::class)->nullable()->after('school_id')->index()->constrained()->nullOnDelete();
            $table->foreignIdFor(Branch::class)->nullable()->after('state_id')->index()->constrained()->nullOnDelete();
            $table->string('name_en')->after('name')->nullable();
            $table->date('birth_date')->after('email')->nullable();
            $table->enum('education_level', ['excellent', 'normal', 'needs_follow_up'])->after('birth_date')->nullable();
            $table->boolean('has_learning_difficulties')->after('education_level')->default(0);
            $table->enum('educational_system', ['monolingual', 'bilingual'])->after('has_learning_difficulties')->nullable();
            $table->json('behavioral_data')->after('educational_system')->nullable();
            $table->enum('pronunciation', ['excellent', 'good', 'needs_follow_up'])->after('behavioral_data')->nullable();
            $table->text('chronic_diseases')->after('pronunciation')->nullable();
            $table->text('food_allergies')->after('chronic_diseases')->nullable();
            $table->text('other_notes')->after('food_allergies')->nullable();
            $table->enum('transport', ['school_bus', 'private_bus'])->after('other_notes')->nullable();
            $table->string('street_number')->after('transport')->nullable();
            $table->string('house_number')->after('street_number')->nullable();
            $table->string('nearest_landmark')->after('house_number')->nullable();
            $table->text('home_location_url')->after('nearest_landmark')->nullable();
            $table->string('siblings_count')->after('home_location_url')->nullable();
            $table->string('parent_identity_card_image')->after('siblings_count')->nullable();
            $table->string('student_residence_card_image')->after('parent_identity_card_image')->nullable();
            $table->string('image')->after('student_residence_card_image')->nullable();
            $table->string('student_passport_image')->after('image')->nullable();
            $table->string('student_birth_certificate_image')->after('student_passport_image')->nullable();
            $table->string('student_health_card_image')->after('student_birth_certificate_image')->nullable();
            $table->string('home_map_image')->after('student_health_card_image')->nullable();
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
