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
        Schema::create('transaction_carts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('name');
            $table->string('description');
            $table->string('brand_name');
            $table->string('category_name');
            $table->integer('quantity');
            $table->decimal('price', 8, 2);
            $table->decimal('total', 8, 2);
            $table->string('image')->nullable();
            $table->timestamps();
            $table->integer('user_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_carts');
    }
};
