<?php

use Illuminate\Support\Facades\Schema;
use Modules\Subject\App\Models\Subject;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Teacher\App\Models\TeacherProfile;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('teacher_subject', function (Blueprint $table) {
            $table->foreignIdFor(TeacherProfile::class, 'teacher_id')->index()->constrained('teacher_profiles')->cascadeOnDelete();
            $table->foreignIdFor(Subject::class, 'subject_id')->index()->constrained()->cascadeOnDelete();
            $table->primary(['teacher_id', 'subject_id']);
            $table->unique(['teacher_id', 'subject_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_subject');
    }
};
