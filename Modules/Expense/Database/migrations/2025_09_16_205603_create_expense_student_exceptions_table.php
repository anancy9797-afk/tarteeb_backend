<?php

use Illuminate\Support\Facades\Schema;
use Modules\Expense\App\Models\Expense;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Student\App\Models\Student;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('expense_student_exceptions', function (Blueprint $table) {
            $table->foreignIdFor(Expense::class)->index()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Student::class)->index()->constrained()->restrictOnDelete();
            $table->unsignedInteger('exception_price');
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->primary(['expense_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_student_exceptions');
    }
};
