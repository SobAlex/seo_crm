<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keyword_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('keyword_id')->constrained()->onDelete('cascade');
            $table->string('search_engine', 50);      // google, yandex
            $table->string('region', 50);             // ru, moscow, etc.
            $table->integer('position')->nullable();  // позиция или null
            $table->string('url')->nullable();        // найденный URL
            $table->timestamp('checked_at');
            $table->timestamps();

            $table->index(['keyword_id', 'checked_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keyword_positions');
    }
};
