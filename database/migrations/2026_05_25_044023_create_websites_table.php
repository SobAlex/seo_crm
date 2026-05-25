<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('websites', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('website_type_id')->constrained('website_types')->onDelete('cascade');
            $table->string('cms')->nullable();
            $table->string('region')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('websites');
    }
};
