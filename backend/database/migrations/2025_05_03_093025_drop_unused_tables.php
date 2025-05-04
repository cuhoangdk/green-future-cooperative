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
        Schema::dropIfExists('contact_informations');
        Schema::dropIfExists('social_links');

        Schema::table('posts', function (Blueprint $table) {
            $table->text('summary')->change(); // Update 'summary' column to allow longer content
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
