<?php

use Illuminate\Support\Facades\Schema;
use Modules\Student\App\Models\Student;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_parent', function (Blueprint $table) {
            $table->foreignIdFor(Student::class)->primary()->constrained()->cascadeOnDelete();
            $table->string('parent_name')->nullable();
            $table->string('parent_nationality')->nullable();
            $table->string('parent_identity_number')->nullable();
            $table->string('parent_job')->nullable();
            $table->string('parent_job_address')->nullable();
            $table->string('parent_education_level')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_nationality')->nullable();
            $table->string('mother_identity_number')->nullable();
            $table->string('mother_job')->nullable();
            $table->string('mother_job_address')->nullable();
            $table->string('mother_education_level')->nullable();
            $table->string('mother_phone')->nullable();
            $table->enum('parents_status', ['together', 'separated', 'widower', 'widow'])->nullable();
            $table->string('relative_name')->nullable();
            $table->string('relative_relation')->nullable();
            $table->string('relative_phone')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_parent');
    }
};
