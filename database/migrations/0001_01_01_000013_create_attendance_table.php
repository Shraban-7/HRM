<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->date('date');
            $table->timestamp('clock_in')->nullable();
            $table->timestamp('clock_out')->nullable();
            $table->string('status')->default('present');
            $table->decimal('overtime_hours', 5, 2)->nullable()->default(0);
            $table->text('notes')->nullable();
            $table->unique(['employee_id', 'date'], 'unique_employee_date');
            $table->index(['employee_id', 'date', 'status'], 'idx_attendance_emp_date_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
