<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('metrika_counters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained()->onDelete('cascade');
            $table->string('counter_id');
            $table->text('token')->nullable();         // access_token (шифровать)
            $table->timestamp('token_expires_at')->nullable();
            $table->json('goals')->nullable();         // цели счётчика
            $table->timestamp('last_sync_at')->nullable();
            $table->string('sync_status')->nullable(); // ok, error
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('metrika_counters');
    }
};
