<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Modules\Country\App\Models\Governorate;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('governorates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

    Governorate::insert([
        ['name' => 'مسقط', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'ظفار', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'مسندم', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'البريمي', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'الداخلية', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'شمال الباطنة', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'جنوب الباطنة', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'جنوب الشرقية', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'شمال الشرقية', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'الظاهرة', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'الوسطى', 'created_at' => now(), 'updated_at' => now()],
    ]);
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('governorates');
    }
};
