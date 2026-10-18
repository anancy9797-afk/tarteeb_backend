<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('student_parent', function (Blueprint $table) {
            $table->enum('parents_status', ['together', 'separated', 'widower', 'widow', 'father_dead', 'mother_dead'])->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_parent', function (Blueprint $table) {

        });
    }
};
