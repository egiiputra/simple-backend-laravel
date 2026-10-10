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
        Schema::create('couriers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone', 20)->unique();
            $table->unsignedTinyInteger('level');
            $table->string('vehicle_type', 20);
            $table->string('vehicle_plate', 20)->nullable()->unique();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('registered_at');
            $table->timestamps();

            $table->index('name');
            $table->index('level');
            $table->index('registered_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('couriers');
    }
};
