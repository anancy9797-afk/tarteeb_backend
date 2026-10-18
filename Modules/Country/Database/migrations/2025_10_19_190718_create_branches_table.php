<?php

use Modules\School\App\Models\School;
use Illuminate\Support\Facades\Schema;
use Modules\Country\App\Models\Branch;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignIdFor(School::class)->index()->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Branch::insert([
            ['name' => 'Main Branch', 'school_id' => 4, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
