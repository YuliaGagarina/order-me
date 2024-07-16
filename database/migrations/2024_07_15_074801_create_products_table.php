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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_id')->nullable(false)->unique();
            $table->integer('vendor_id')->nullable(false);
            $table->string('title')->default(null);
            $table->text('description')->default(null);
            $table->string('image_url')->default(null);
            $table->string('upc')->default(null);
            $table->integer('quantity')->default(0);
            $table->dateTime('quantity_updated_at')->default(null);
            $table->float('fmv')->default(0);
            $table->boolean('nominated')->default(false);
            $table->date('nominated_date_start')->default(null);
            $table->date('nominated_date_end')->default(null);
            $table->timestamps();

            $table->unique(['product_id', 'vendor_id']);
            $table->foreign('vendor_id')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
