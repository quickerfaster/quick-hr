<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies', 'id')->onDelete('cascade');
            $table->index('company_id');
            $table->foreignId('employee_id')->nullable()->constrained('employees', 'id')->onDelete('restrict');
            $table->date('date');
            $table->foreignId('shift_id')->nullable()->constrained('shifts', 'id')->onDelete('restrict');
            $table->string('status')->default('present');
            $table->decimal('net_hours', 6, 2)->nullable();
            $table->decimal('regular_hours', 6, 2)->default(0);
            $table->decimal('overtime_hours', 6, 2)->default(0);
            $table->decimal('double_time_hours', 6, 2)->default(0);
            $table->string('absence_type')->nullable();
            $table->text('absence_reason')->nullable();
            $table->foreignId('leave_request_id')->nullable()->constrained('leave_requests', 'id')->onDelete('set null');
            $table->boolean('is_paid_absence')->default(true);
            $table->decimal('hours_deducted', 6, 2)->default(0)->nullable();
            $table->boolean('is_approved')->default(false);
            $table->boolean('needs_review')->default(true);
            $table->text('notes')->nullable();
            $table->integer('minutes_late')->default(0);
            $table->integer('minutes_early_departure')->default(0);
            $table->integer('missed_break_minutes')->default(0);
            $table->json('sessions')->nullable();
            $table->string('approved_by')->nullable();
            $table->datetime('approved_at')->nullable();
            $table->datetime('last_calculated_at')->nullable();
            $table->string('calculation_method')->default('auto')->nullable();
            $table->foreignId('attendance_policy_id')->nullable()->constrained('attendance_policies', 'id')->onDelete('set null');
            $table->foreignId('work_pattern_id')->nullable()->constrained('work_patterns', 'id')->onDelete('set null');
            $table->boolean('is_unplanned')->default(false);
            $table->json('calculation_metadata')->nullable();
            $table->string('calculation_version')->nullable();

            			$table->index('employee_id');
			$table->index('date');
			$table->index('status');
			$table->index('is_approved');
			$table->index('needs_review');
			$table->index('leave_request_id');
			$table->index('deleted_at');
			$table->index(['employee_id', 'date']);
			$table->index(['status', 'date']);
			$table->index(['is_approved', 'date']);
			$table->index(['needs_review', 'date']);
			$table->unique(['employee_id', 'date']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendances');
    }
};
