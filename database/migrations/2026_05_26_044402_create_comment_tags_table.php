<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comment_tags', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('color')->default('#cccccc');
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        // Добавляем системную метку "Для отчета"
        DB::table('comment_tags')->insert([
            'title' => 'Для отчета',
            'color' => '#27ae60',
            'is_system' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('comment_tags');
    }
};
