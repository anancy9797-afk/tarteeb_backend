<?php

use Modules\School\App\Models\School;
use Illuminate\Support\Facades\Schema;
use Modules\Class\App\Models\Classroom;
use Modules\Subject\App\Models\Subject;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Teacher\App\Models\TeacherProfile;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->id();
            $table->enum('day', ['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday']);
            $table->integer('session_number');
            $table->enum('semester', ['first', 'second'])->nullable();
            $table->string('year')->nullable();
            $table->foreignIdFor(Classroom::class, 'class_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Subject::class)->index()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(School::class)->index()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(TeacherProfile::class, 'teacher_id')->nullable()->index()->constrained('teacher_profiles')->nullOnDelete();
            $table->boolean('is_final')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};