<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plannings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained()->onDelete('cascade');
            $table->foreignId('track_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->date('period_start');
            $table->date('period_end');
            $table->string('metric_type');
            $table->string('metric_label')->nullable();
            $table->decimal('target_value', 10, 2);
            $table->decimal('alert_threshold', 5, 2)->default(20);
            $table->boolean('completed_notification_sent')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plannings');
    }
};
