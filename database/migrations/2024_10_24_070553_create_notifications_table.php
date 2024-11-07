<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('user_id_send');
            $table->foreign('user_id_send')->references('id')->on('users')->onDelete('cascade');
            $table->text('content')->nullable();
            $table->unsignedBigInteger('user_id_receive');
            $table->foreign('user_id_receive')->references('id')->on('users')->onDelete('cascade');
            $table->string('link');
            $table->string('key_word')->nullable();
            $table->boolean('isSaw')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
