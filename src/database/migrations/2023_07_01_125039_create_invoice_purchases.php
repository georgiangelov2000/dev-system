<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicePurchases extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_payment_id');
            $table->string('invoice_number')->unique();
            $table->date('invoice_date');
            $table->unsignedDecimal('price');
            $table->unsignedInteger('quantity');
            $table->tinyInteger('status')->comment('1: Projecting, 2: Paid,3: Not paid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_purchases');
    }
}
