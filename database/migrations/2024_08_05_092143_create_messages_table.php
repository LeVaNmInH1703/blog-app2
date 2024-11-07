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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('type')->default('default');
            $table->unsignedBigInteger('user_id_send');
            $table->foreign('user_id_send')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('group_id_receive');
            $table->foreign('group_id_receive')->references('id')->on('group_chats')->onDelete('cascade');
            $table->text('content')->nullable(true);
            $table->boolean('isSaw')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
