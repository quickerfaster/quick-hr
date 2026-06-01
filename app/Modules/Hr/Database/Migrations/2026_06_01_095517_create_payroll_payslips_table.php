<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payroll_payslips', function (Blueprint $table) {
            $table->id();
            $table->string('payslip_number');
            $table->foreignId('payroll_run_id')->constrained('payroll_runs', 'id')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('employees', 'id')->onDelete('restrict');
            $table->decimal('base_salary', 12, 2);
            $table->decimal('gross_pay', 12, 2);
            $table->decimal('total_deductions', 12, 2)->default(0);
            $table->decimal('total_taxes', 12, 2)->default(0)->nullable();
            $table->decimal('total_benefit_deductions', 12, 2)->default(0)->nullable();
            $table->decimal('net_pay', 12, 2);
            $table->string('payment_status')->default('pending');
            $table->datetime('paid_at')->nullable();
            $table->string('payment_reference')->nullable();
            $table->json('bank_account_snapshot')->nullable();
            $table->text('notes')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            
            			$table->index('payslip_number');
			$table->index('payroll_run_id');
			$table->index('employee_id');
			$table->index('payment_status');
			$table->index('paid_at');
			$table->index('net_pay');
			$table->index('created_at');
			$table->index(['payroll_run_id', 'employee_id']);
			$table->index(['payroll_run_id', 'payment_status']);
			$table->index(['employee_id', 'payment_status']);
			$table->index(['employee_id', 'paid_at']);
			$table->unique(['payroll_run_id', 'employee_id']);
			$table->unique('payslip_number');
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payroll_payslips');
    }
};
