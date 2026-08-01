<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->constrained();
            $table->string('employee_id')->unique();
            $table->date('hire_date');
            $table->string('designation');
            $table->decimal('salary', 12, 2)->default(0);
            $table->string('status')->default('active');
            $table->string('profile_photo_path')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('phone')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('blood_group')->nullable();
            $table->text('address')->nullable();
            $table->string('employment_type')->default('full_time');
            $table->date('probation_ends_at')->nullable();
            $table->foreignId('manager_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->index(['department_id', 'status'], 'idx_employees_dept_status');
            $table->index(['manager_id'], 'idx_employees_manager_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
