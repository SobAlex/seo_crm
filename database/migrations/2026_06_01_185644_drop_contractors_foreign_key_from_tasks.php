<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Удаляем существующий внешний ключ (имя может отличаться, но обычно это "tasks_assignee_contractor_id_foreign")
            $table->dropForeign(['assignee_contractor_id']);
            // Добавляем новый внешний ключ на users.id
            $table->foreign('assignee_contractor_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['assignee_contractor_id']);
            $table->foreign('assignee_contractor_id')->references('id')->on('contractors')->onDelete('set null');
        });
    }
};
