<?php

use Modules\Grade\App\Models\Grade;
use Modules\School\App\Models\School;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignIdFor(Grade::class)->index()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(School::class)->index()->constrained()->cascadeOnDelete();
            $table->enum('semester', ['first', 'second']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
