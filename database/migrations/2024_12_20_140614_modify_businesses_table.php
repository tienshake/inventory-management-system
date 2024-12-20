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
        Schema::table('businesses', function (Blueprint $table) {
            $table->renameColumn('name', 'company_name');

            $table->text('address');

            $table->dropColumn('business_type');

            $table->foreignId('business_type_id')
                ->constrained()
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->renameColumn('company_name', 'name');
            $table->dropColumn('address');
            $table->dropForeign(['business_type_id']);
            $table->dropColumn('business_type_id');
            $table->enum('business_type', ['customer', 'supplier', 'internal']);
        });
    }
};
