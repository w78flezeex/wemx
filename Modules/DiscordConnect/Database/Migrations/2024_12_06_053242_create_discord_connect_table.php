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
        Schema::create('module_dc_packages_events', function (Blueprint $table) {
            $table->id();
            $table->json('packages');
            $table->boolean('all_packages')->default(false);
            $table->string('name');
            $table->string('event');
            $table->string('action');
            $table->json('roles');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_dc_packages_events');
    }
};
