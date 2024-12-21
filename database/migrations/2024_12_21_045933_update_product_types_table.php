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
        Schema::table('product_types', function (Blueprint $table) {
            $table->dropColumn('manufacturer');
            $table->dropColumn('category');
            $table->dropColumn('sub_category');

            $table->foreignId('manufacturer_id')->after('model_sku')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->after('manufacturer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sub_category_id')->after('category_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_types', function (Blueprint $table) {
            // Xóa các cột foreign key
            $table->dropForeign(['manufacturer_id']);
            $table->dropForeign(['category_id']);
            $table->dropForeign(['sub_category_id']);
            $table->dropColumn(['manufacturer_id', 'category_id', 'sub_category_id']);

            // Thêm lại các cột cũ
            $table->string('manufacturer');
            $table->string('category');
            $table->string('sub_category')->nullable();
        });
    }
};
