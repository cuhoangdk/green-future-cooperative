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
        Schema::create('posts', function (Blueprint $table) {
            $table->id()->from(100);
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('summary');
            $table->text('content');
            $table->string('featured_image')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('author_id')->nullable();
            $table->enum('post_status', ['draft', 'published', 'archived'])->default('draft');                     
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        
            $table->foreign('category_id')->references('id')->on('post_categories')->onDelete('set null');
            $table->foreign('author_id')->references('id')->on('cooperative_members')->onDelete('set null');
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
