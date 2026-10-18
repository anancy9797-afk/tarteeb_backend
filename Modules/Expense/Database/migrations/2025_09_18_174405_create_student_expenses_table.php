<?php

use Illuminate\Support\Facades\Schema;
use Modules\Expense\App\Models\Expense;
use Modules\Student\App\Models\Student;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Student::class)->index()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Expense::class)->index()->constrained()->cascadeOnDelete();
            $table->unsignedInteger('amount')->nullable();
            $table->unsignedInteger('amount_paid')->nullable();
            $table->enum('payment_status', ['partial', 'full'])->nullable();
            $table->date('date')->nullable();
            $table->unsignedTinyInteger('payment_method')->comment('1: cash, 2: visa 3:online payment');
            $table->string('receipt')->nullable();
            $table->string('rejected_reason')->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_expenses');
    }
};
