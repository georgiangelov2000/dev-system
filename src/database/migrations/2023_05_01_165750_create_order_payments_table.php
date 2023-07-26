<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedDecimal('price',8,2)->default(0);
            $table->tinyInteger('payment_method')->nullable()->comment('1: Cash, 2: Bank Transfer, 3: Credit Card, 4: Cheque, 5: Online Payment');
            $table->string('payment_reference')->default('N/A');
            $table->tinyInteger('payment_status')->nullable()->comment('1: Paid, 2: Pending, 3: Partially Paid, 4: Overdue, 5: Refunded, 6: Ordered');
            $table->date('date_of_payment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_payments');
    }
}
