<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Minimal per-user profile captured during onboarding. Only fields needed
 * for the chosen account type are filled; everything else stays NULL.
 * No date of birth, address or ID numbers are collected.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('account_type', 32);            // App\Enums\AccountType
            $table->string('phone', 32)->nullable();
            $table->string('class_level', 32)->nullable();  // students: e.g. ss2
            $table->json('exam_goals')->nullable();         // students: ["waec","jamb"]
            $table->string('organisation_name', 160)->nullable(); // school / CBT centre / teacher's school
            $table->string('state', 64)->nullable();        // Nigerian state of the organisation
            $table->timestamp('onboarded_at')->nullable();
            $table->timestamps();

            $table->index('account_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
