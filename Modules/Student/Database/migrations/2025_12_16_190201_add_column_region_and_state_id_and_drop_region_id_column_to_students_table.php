<?php

use Modules\Country\App\Models\State;
use Illuminate\Support\Facades\Schema;
use Modules\Country\App\Models\Region;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->foreignIdFor(State::class)->nullable()->after('region_id')->index()->constrained()->nullOnDelete();
            $table->dropForeign(['region_id']);
            $table->dropColumn('region_id');
            $table->string('region')->after('state_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['state_id']);
            $table->dropColumn('state_id');
            $table->foreignIdFor(Region::class)->nullable()->after('region_id')->index()->constrained()->nullOnDelete();
            $table->dropColumn('region');
        });
    }
};
