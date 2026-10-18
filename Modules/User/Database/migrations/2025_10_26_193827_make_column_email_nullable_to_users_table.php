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
        DB::statement('ALTER TABLE users MODIFY COLUMN email VARCHAR(255) NULL UNIQUE');
        DB::statement('ALTER TABLE students MODIFY COLUMN email VARCHAR(255) NULL UNIQUE');
        DB::statement('ALTER TABLE students MODIFY COLUMN identity_number VARCHAR(255) UNIQUE');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
