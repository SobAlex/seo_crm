<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('track_id')->constrained()->onDelete('cascade');
            $table->foreignId('status_id')->constrained('process_statuses')->onDelete('cascade');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->date('deadline')->nullable();
            $table->foreignId('assignee_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('assignee_contractor_id')->nullable()->constrained('contractors')->onDelete('set null');
            $table->foreignId('created_by_id')->constrained('users')->onDelete('cascade');
            $table->json('checklist')->nullable();
            $table->json('structure')->nullable();
            $table->json('files')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
