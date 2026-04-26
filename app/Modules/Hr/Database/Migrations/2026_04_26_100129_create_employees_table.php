<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies', 'id')->onDelete('set null');
            $table->string('employee_number');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone')->nullable();
            $table->date('hire_date');
            $table->string('email')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users', 'id')->onDelete('set null');
            
            			$table->index('company_id');
			$table->index('employee_number');
			$table->index('first_name');
			$table->index('last_name');
			$table->index(['employee_number', 'company_id']);
			$table->index(['first_name', 'last_name']);
			$table->unique('employee_number');
			$table->unique('email');
			$table->unique('user_id');
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employees');
    }
};
