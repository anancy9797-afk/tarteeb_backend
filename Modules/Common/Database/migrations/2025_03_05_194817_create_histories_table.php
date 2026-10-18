<?php

use Modules\School\App\Models\School;
use Illuminate\Support\Facades\Schema;
use Modules\Class\App\Models\Classroom;
use Modules\Session\App\Models\Session;
use Modules\Student\App\Models\Student;
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
        Schema::create('histories', function (Blueprint $table) {
            $table->id();
            $table->enum('day', ['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday']);
            $table->integer('session_number');
            $table->enum('semester', ['first', 'second']);
            $table->integer('year');
            $table->foreignIdFor(TeacherProfile::class, 'teacher_id')->nullable()->index()->constrained('teacher_profiles')->nullOnDelete();
            $table->foreignIdFor(TeacherProfile::class, 'attendance_taken_by')->nullable()->index()->constrained('teacher_profiles')->nullOnDelete();
            $table->foreignIdFor(Student::class)->index()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Subject::class)->index()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Session::class)->index()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Classroom::class, 'class_id')->index()->constrained('classes')->cascadeOnDelete();
            $table->foreignIdFor(School::class)->index()->constrained()->cascadeOnDelete();
            $table->string('teacher_name');
            $table->string('attendance_taken_by_name');
            $table->string('student_name');
            $table->string('subject_name');
            $table->string('class_name');
            $table->string('school_name');
            $table->boolean('is_present')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('histories');
    }
};