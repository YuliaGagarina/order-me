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
        Schema::create('campaign_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns');
            $table->string('product_id');
            $table->integer('variation_id')->nullable()->default(null);
            $table->integer('vendor_id');
            $table->integer('quantity');
            $table->integer('initial_quantity');
            $table->decimal('fmv', 5, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_products');
    }
};
