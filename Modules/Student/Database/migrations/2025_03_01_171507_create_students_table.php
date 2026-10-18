<?php

use Modules\Grade\App\Models\Grade;
use Modules\School\App\Models\School;
use Illuminate\Support\Facades\Schema;
use Modules\Class\App\Models\Classroom;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('gender', ['m', 'f']);
            $table->string('email')->unique();
            $table->string('identity_number');
            $table->string('parent_email')->nullable()->unique();
            $table->string('parent_phone');
            $table->foreignIdFor(Grade::class)->index()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Classroom::class, 'class_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(School::class)->index()->constrained()->cascadeOnDelete();
            $table->boolean("is_graduated")->default(0);
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
