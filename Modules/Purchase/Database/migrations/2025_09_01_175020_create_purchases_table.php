<?php

use Modules\User\App\Models\User;
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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->text('description')->nullable();
            $table->foreignIdFor(User::class)->nullable()->index()->constrained()->nullOnDelete();
            $table->foreignIdFor(School::class)->index()->constrained()->cascadeOnDelete();
            $table->string('image')->nullable();
            $table->date('date')->nullable();
            $table->unsignedDecimal('price', 10, 2)->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->string('reject_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
