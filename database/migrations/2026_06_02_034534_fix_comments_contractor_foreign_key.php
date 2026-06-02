<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('comments', function (Blueprint $table) {
            // Удаляем внешний ключ, если он существует
            $table->dropForeign(['contractor_id']);
            // Пересоздаём ссылку на таблицу users
            $table->foreign('contractor_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['contractor_id']);
            $table->foreign('contractor_id')->references('id')->on('contractors')->onDelete('set null');
        });
    }
};
