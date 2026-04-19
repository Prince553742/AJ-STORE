<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            // Drop the old columns
            $table->dropColumn(['order_date', 'expected_delivery']);
            // Add the new single date column
            $table->date('purchase_date')->nullable()->after('po_number');
        });
    }

    public function down()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn('purchase_date');
            $table->date('order_date')->nullable();
            $table->date('expected_delivery')->nullable();
        });
    }
};