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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['Super Admin', 'School Manager', 'Teacher', 'Employee', 'Student', 'Financial Director', 'Sales Employee', 'Purchasing Employee', 'Salaries Employee', 'Maintenance Employee', 'Other'])->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('column_role_nullable_to_users');
    }
};
