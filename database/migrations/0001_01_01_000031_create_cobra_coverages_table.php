<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cobra_coverages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('benefit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('benefit_enrollment_id')->nullable()->constrained()->nullOnDelete();
            $table->date('qualifying_event_date');
            $table->date('coverage_start_date');
            $table->date('coverage_end_date');
            $table->enum('status', ['active', 'expired', 'terminated'])->default('active');
            $table->decimal('premium_amount', 12, 2)->default(0);
            $table->timestamps();

            $table->index('status');
            $table->index('coverage_end_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cobra_coverages');
    }
};
