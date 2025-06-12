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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('source');
            $table->string('plugin_name');
            $table->string('invoice_id')->unique(); //
            $table->string('order_id')->unique(); //
            $table->string('customer_email'); //
            $table->string('product'); //
            $table->string('quantity');
            $table->integer('credit_points');
            $table->dateTime('date_purchase');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
