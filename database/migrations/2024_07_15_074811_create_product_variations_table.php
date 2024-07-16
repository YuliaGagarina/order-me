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
        Schema::create('product_variations', function (Blueprint $table) {
            $table->id();
            $table->string('product_id');
            $table->integer('vendor_id');
            $table->integer('quantity')->nullable(false)->default(0);
            $table->string('image_url');
            $table->string('upc');
            $table->timestamps();

            $table->foreign(['product_id', 'vendor_id'])
                ->references(['product_id', 'vendor_id'])
                ->on('products')
                ->onDelete('cascade');

            $table->index(['product_id', 'vendor_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variations');
    }
};
