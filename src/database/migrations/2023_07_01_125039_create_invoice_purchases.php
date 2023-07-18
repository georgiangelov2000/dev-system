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
            $table->unsignedBigInteger('purchase_payment_id');
            $table->string('invoice_number')->nullable()->unique();
            $table->date('invoice_date')->nullable();
            $table->unsignedDecimal('price')->default(0);
            $table->unsignedInteger('quantity')->default(0);
            $table->tinyInteger('status')->default(2)->comment('1: Paid,2: Not paid');
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
