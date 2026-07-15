<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies', 'id')->onDelete('cascade');
            $table->index('company_id');
            $table->string('name');
            $table->string('code');
            $table->text('description')->nullable();
            $table->foreignId('team_lead_id')->nullable()->constrained('employees', 'id')->onDelete('set null');
            $table->boolean('is_active')->default(true);

            			$table->index('name');
			$table->index('code');
			$table->index('is_active');
			$table->index('team_lead_id');
			$table->index('deleted_at');
			$table->index(['code', 'is_active']);
			$table->unique('name');
			$table->unique('code');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('teams');
    }
};
