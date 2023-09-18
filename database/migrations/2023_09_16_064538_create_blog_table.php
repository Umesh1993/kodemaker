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
        Schema::create('blog', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('title','200');
            $table->string('slug','250');
            $table->string('description','300');
            $table->text('blog_image');
            $table->text('page_description');
            $table->enum('is_active', ['0', '1'])->default('1');
            $table->enum('is_delete', ['0', '1'])->default('0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog');
    }
};
