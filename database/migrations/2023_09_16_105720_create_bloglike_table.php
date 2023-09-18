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
        Schema::create('bloglike', function (Blueprint $table) {
            $table->id();
            $table->integer('blog_id');
            $table->integer('user_id')->nullable();
            $table->integer('guest')->nullable();
            $table->string('ip')->nullable();
            $table->string('like')->comment('1: like; 0: unlike;');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bloglike');
    }
};
