<?php

use Illuminate\Support\Facades\Schema;
use Modules\Country\App\Models\Region;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('register_fee_image')->after('is_fee_paid')->nullable();
            $table->dropForeign(['state_id']);
            $table->dropColumn('state_id');
            $table->foreignIdFor(Region::class)->nullable()->after('school_id')->index()->constrained()->nullOnDelete();
            $table->boolean('is_register_fee_accepted')->after('register_fee_image')->nullable();
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
