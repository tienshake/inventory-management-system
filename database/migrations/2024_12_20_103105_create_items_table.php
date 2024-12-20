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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_type_id')->constrained();
            $table->string('serial_number')->unique();
            $table->enum('status', [
                'in_warehouse',
                'on_downpayment',
                'on_lease',
                'sold',
                'in_transit'
            ]);
            $table->foreignId('business_location_id')->nullable()->constrained();
            $table->foreignId('warehouse_id')->nullable()->constrained();
            $table->string('storage_location')->nullable();
            $table->date('purchase_date');
            $table->decimal('purchase_price', 15, 2);
            $table->date('lease_start_date')->nullable();
            $table->date('lease_end_date')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
