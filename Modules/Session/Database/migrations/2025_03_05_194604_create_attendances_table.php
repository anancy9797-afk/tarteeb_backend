<?php

use Illuminate\Support\Facades\Schema;
use Modules\Session\App\Models\Session;
use Modules\Student\App\Models\Student;
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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            // $table->foreignIdFor(Session::class)->index()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Session::class)->nullable()->index()->constrained()->nullOnDelete();
            $table->foreignIdFor(Student::class)->index()->constrained()->cascadeOnDelete();
            $table->boolean('is_present')->default(0);
            $table->foreignIdFor(TeacherProfile::class, 'teacher_id')->nullable()->index()->constrained('teacher_profiles')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
