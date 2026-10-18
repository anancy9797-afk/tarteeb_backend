<?php

use Modules\User\App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->foreignIdFor(User::class)->after('parent_phone')->nullable()->index()->constrained()->cascadeOnDelete();
            $table->boolean('is_fee_paid')->after('user_id')->default(0);
            $table->string('application_form')->after('is_fee_paid')->nullable();
            $table->boolean('is_register')->after('is_active')->default(0);
            $table->dropForeign(['class_id']);
            $table->unsignedBigInteger('class_id')->nullable()->change();
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {

        });
    }
};
