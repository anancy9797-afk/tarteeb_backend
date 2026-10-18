<?php

use Illuminate\Support\Facades\DB;
use Modules\School\App\Models\School;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Schema::create('employees', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('name');
        //     $table->string('phone')->unique()->nullable();
        //     $table->string('email')->unique();
        //     $table->string('password');
        //     $table->string('image')->nullable();
        //     $table->foreignIdFor(School::class)->index()->constrained()->cascadeOnDelete();
        //     $table->boolean('is_active')->default(1);
        //     $table->timestamps();
        // });
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['Super Admin', 'School Manager', 'Teacher', 'Employee', 'Student', 'Financial Director', 'Sales Employee', 'Purchasing Employee', 'Salaries Employee', 'Maintenance Employee'])->change();
        });
        DB::table('roles')->insert([
            ['name' => 'Financial Director', 'guard_name' => 'user', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sales Employee', 'guard_name' => 'user', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Purchasing Employee', 'guard_name' => 'user', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Salaries Employee', 'guard_name' => 'user', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Maintenance Employee', 'guard_name' => 'user', 'created_at' => now(), 'updated_at' => now()],
        ]);
        // DB::table('permissions')->insert([
        //     ['name' => 'Financial Director', 'guard_name' => 'employee', 'category' => 'Financial', 'display' => 'Financial Director', 'created_at' => now(), 'updated_at' => now()],
        //     ['name' => 'Sales Employee', 'guard_name' => 'employee', 'category' => 'Sales', 'display' => 'Sales Employee', 'created_at' => now(), 'updated_at' => now()],
        //     ['name' => 'Purchasing Employee', 'guard_name' => 'employee', 'category' => 'Purchasing', 'display' => 'Purchasing Employee', 'created_at' => now(), 'updated_at' => now()],
        //     ['name' => 'Salaries Employee', 'guard_name' => 'employee', 'category' => 'Salaries', 'display' => 'Salaries Employee', 'created_at' => now(), 'updated_at' => now()],
        //     ['name' => 'Maintenance Employee', 'guard_name' => 'employee', 'category' => 'Maintenance', 'display' => 'Maintenance Employee', 'created_at' => now(), 'updated_at' => now()],
        // ]);
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
