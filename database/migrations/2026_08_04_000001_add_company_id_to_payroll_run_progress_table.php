<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('payroll_run_progress', 'company_id')) {
            Schema::table('payroll_run_progress', function (Blueprint $table) {
                $table->foreignId('company_id')->nullable()->after('payroll_run_id');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('payroll_run_progress', 'company_id')) {
            Schema::table('payroll_run_progress', function (Blueprint $table) {
                $table->dropColumn('company_id');
            });
        }
    }
};
