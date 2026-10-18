<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            $table->unsignedInteger('deduction')->default(0);
            $table->string('deduction_reason')->nullable();
            $table->unsignedInteger('bonus')->default(0);
            $table->string('bonus_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            $table->dropColumn('deduction');
            $table->dropColumn('deduction_reason');
            $table->dropColumn('bonus');
            $table->dropColumn('bonus_reason');
        });
    }
};
