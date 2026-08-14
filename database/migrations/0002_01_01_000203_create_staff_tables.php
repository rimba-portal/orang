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
        Schema::create('staff', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->foreignId('org_corp_id')->nullable()->constrained();
            $table->foreignId('org_unit_id')->nullable()->constrained();
            $table->foreignId('job_contract_id')->nullable()->constrained('agreements');
            $table->enum('type', ['FTE', 'FTC', 'TPC', 'Intern'])->default('FTE');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->string('name')->nullable();
            $table->string('staff_no')->nullable()->unique();
            $table->json('attributes')->nullable();
            $table->timestamps();
        });
        Schema::create('staff_positions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('staff_id')->constrained();
            $table->foreignId('job_position_id')->constrained();
            $table->enum('assignment_type', ['primary', 'secondary', 'acting'])->default('primary');
            $table->enum('status', ['active', 'ended', 'pending'])->default('active');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->json('attributes')->nullable();
            $table->timestamps();
        });
        Schema::create('movements', function (Blueprint $table): void {
            $table->id();
            $table->enum('type', ['transfer', 'promotion', 'demotion', 'assignment', 'end_of_assignment']);
            $table->date('effective_date');
            $table->json('from')->nullable();
            $table->json('to')->nullable();
            $table->morphs('movable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movements');
        Schema::dropIfExists('staff_positions');
        Schema::dropIfExists('staff');
    }
};
