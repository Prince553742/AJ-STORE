<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Drop foreign key and column from purchase_orders
        Schema::table('purchase_orders', function (Blueprint $table) {
            // Drop foreign key if it exists (name may vary, try common names)
            $table->dropForeign(['supplier_id']);
            $table->dropColumn('supplier_id');
        });

        // Drop the suppliers table if it exists
        if (Schema::hasTable('suppliers')) {
            Schema::dropIfExists('suppliers');
        }
    }

    public function down()
    {
        // Recreate suppliers table (optional, for rollback)
        if (!Schema::hasTable('suppliers')) {
            Schema::create('suppliers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('contact')->nullable();
                $table->string('phone')->nullable();
                $table->text('address')->nullable();
                $table->timestamps();
            });
        }

        // Re-add supplier_id column to purchase_orders
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
        });
    }
};