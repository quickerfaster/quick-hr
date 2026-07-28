<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('attendance_policies', function (Blueprint $table) {
            // Change decimal to string to support JSON-encoded arrays for multi-break rules
            $table->string('requires_break_after_hours', 100)->nullable()->default('5')->change();
            $table->string('break_duration_minutes', 100)->nullable()->default('30')->change();
        });
    }

    public function down()
    {
        Schema::table('attendance_policies', function (Blueprint $table) {
            $table->decimal('requires_break_after_hours', 4, 2)->default(5)->nullable()->change();
            $table->integer('break_duration_minutes')->default(30)->nullable()->change();
        });
    }
};
