<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planning_facts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('planning_id')->constrained()->onDelete('cascade');
            $table->integer('week_number');
            $table->date('week_start');
            $table->date('week_end');
            $table->integer('days_in_week');
            $table->decimal('actual_value', 10, 2)->nullable();
            $table->decimal('manual_value', 10, 2)->nullable();
            $table->string('source')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamp('manual_override_at')->nullable();
            $table->foreignId('manual_override_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->unique(['planning_id', 'week_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planning_facts');
    }
};
