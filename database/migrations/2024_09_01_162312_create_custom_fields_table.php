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
        Schema::create('custom_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('custom_group_id')->nullable();
            $table->unsignedBigInteger('sort_order')->default(1);
            $table->foreign('custom_group_id')->references('id')->on('custom_groups')->onDelete('set null');
            $table->string('slug')->nullable();
            $table->string('title');
            $table->boolean('is_active')->default(true);
            $table->string('href')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_fields');
    }
};
