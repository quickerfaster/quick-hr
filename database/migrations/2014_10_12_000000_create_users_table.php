<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id')->nullable();
            $table->string('name');
            $table->string('email')->unique(); // Unique index handles lookups automatically
            $table->timestamp('email_verified_at')->nullable(); // Using standard timestamp
            $table->string('status')->default("active");
            $table->string('password'); // Changed from text() to string() for standard hash storage
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();
            $table->string('user_type')->default('user');
            $table->boolean('has_seen_tour')->default(false);
            $table->rememberToken(); // Replaced with standard Laravel helper
            $table->timestamps();
            $table->softDeletes();

            // Performance Indexes
            $table->index('status');
            $table->index('deleted_at');
            $table->index(['status', 'email']); // Composite index for filtered lookups
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
