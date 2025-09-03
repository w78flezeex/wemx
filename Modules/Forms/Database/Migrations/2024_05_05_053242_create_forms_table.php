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
        Schema::create('module_forms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title');
            $table->longText('description')->nullable();
            $table->string('slug')->unique();
            $table->float('price')->default(0);
            $table->json('allowed_gateways')->nullable();
            $table->json('required_packages')->nullable();
            $table->string('notification_email')->nullable();
            $table->integer('max_submissions')->default(0)->nullable();
            $table->integer('max_submissions_per_user')->default(0)->nullable();
            $table->boolean('guest')->default(false);
            $table->boolean('can_view_submission')->default(true);
            $table->boolean('can_respond')->default(true);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('module_forms_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form_id');
            $table->string('label');
            $table->text('description')->nullable();
            $table->string('type');
            $table->string('placeholder')->nullable();
            $table->string('default_value')->nullable();
            $table->string('name');
            $table->string('rules')->nullable();
            $table->integer('order')->default(0);
            $table->json('options')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->foreign('form_id')->references('id')->on('module_forms')->onDelete('cascade');

            // make sure name and form_id are unique
            $table->unique(['name', 'form_id']);
        });

        Schema::create('module_forms_submissions', function (Blueprint $table) {
            $table->id();
            $table->uuid('token')->unique();
            $table->unsignedBigInteger('form_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('guest_email')->nullable();
            $table->string('status');
            $table->text('ip_address');
            $table->text('user_agent');
            $table->json('data');
            $table->boolean('paid')->default(false);
            $table->timestamps();

            $table->foreign('form_id')->references('id')->on('module_forms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('module_forms_submissions_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('submission_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('guest_email')->nullable();
            $table->text('ip_address');
            $table->text('user_agent');
            $table->text('message');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_forms');
        Schema::dropIfExists('module_forms_fields');
        Schema::dropIfExists('module_forms_submissions');
        Schema::dropIfExists('module_forms_submissions_messages');
    }
};
