<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payroll_runs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('pay_schedule_id')->constrained('pay_schedules', 'id')->onDelete('restrict');
            $table->date('period_start');
            $table->date('period_end');
            $table->string('status')->default('draft')->nullable();
            $table->integer('current_step')->default(1)->nullable();
            $table->string('calculation_status')->default('pending')->nullable();
            $table->decimal('total_gross_pay', 15, 2)->default(0)->nullable();
            $table->decimal('total_deductions', 15, 2)->default(0)->nullable();
            $table->decimal('total_taxes', 15, 2)->default(0)->nullable();
            $table->decimal('total_employer_contributions', 15, 2)->default(0)->nullable();
            $table->decimal('total_cash_required', 15, 2)->default(0)->nullable();
            $table->string('processed_by')->nullable();
            $table->datetime('processed_at')->nullable();
            $table->string('approved_by')->nullable();
            $table->datetime('approved_at')->nullable();
            $table->integer('total_employees')->default(0)->nullable();
            $table->integer('processed_employees')->default(0)->nullable();
            $table->datetime('failed_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->text('notes')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            
            			$table->index('pay_schedule_id');
			$table->index('status');
			$table->index('period_start');
			$table->index('period_end');
			$table->index('processed_at');
			$table->index('approved_at');
			$table->index('current_step');
			$table->index('created_at');
			$table->index(['pay_schedule_id', 'status']);
			$table->index(['status', 'period_start']);
			$table->index(['period_start', 'period_end']);
			$table->index(['pay_schedule_id', 'period_start', 'period_end']);
			$table->unique('title');
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payroll_runs');
    }
};
