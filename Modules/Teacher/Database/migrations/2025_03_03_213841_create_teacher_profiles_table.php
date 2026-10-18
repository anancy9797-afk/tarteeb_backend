<?php

use Modules\User\App\Models\User;
use Modules\Grade\App\Models\Grade;
use Illuminate\Support\Facades\Schema;
use Modules\Subject\App\Models\Subject;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('teacher_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->unique()->constrained()->cascadeOnDelete();
            $table->enum('gender', ['m', 'f']);
            // $table->foreignIdFor(Grade::class)->nullable()->index()->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_profiles');
    }
};
