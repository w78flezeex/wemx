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
        Schema::create('module_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->string('session_id');
            $table->text('content');
            $table->enum('type', ['user', 'ai', 'system'])->default('user');
            $table->json('metadata')->nullable();
            $table->boolean('is_ai_generated')->default(false);
            $table->timestamps();

            $table->foreign('session_id')->references('session_id')->on('module_chat_sessions')->onDelete('cascade');
            $table->index(['session_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_chat_messages');
    }
};
