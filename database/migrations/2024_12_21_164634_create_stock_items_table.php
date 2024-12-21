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
        Schema::create('stock_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_type_id')->constrained()->cascadeOnDelete();
            $table->string('serial_number')->unique();
            $table->enum('status', [
                'in_warehouse',
                'on_downpayment',
                'on_lease',
                'sold',
                'in_transit'
            ])->default('in_warehouse');

            // References
            $table->foreignId('business_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained()->nullOnDelete();

            // Storage location within warehouse
            $table->string('storage_location')->nullable();

            // Purchase details
            $table->date('date_acquired')->nullable();
            $table->decimal('cost_price', 15, 2)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_items');
    }
};
