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
        Schema::create('sale_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('order_id');
            $table->json('seats')->nullable();
            $table->json('seats_text')->nullable();
            $table->unsignedBigInteger('show_id')->nullable();
            $table->json('show_info')->nullable();
            $table->string('sum')->nullable();
            $table->string('price')->nullable();
            $table->string('sale')->nullable();
            $table->string('reservation_id')->nullable();
            $table->string('currency')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_lists');
    }
};
