<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_payment_id');
            $table->string('invoice_number')->nullable()->unique();
            $table->date('invoice_date')->nullable();
            $table->unsignedDecimal('price')->default(0);
            $table->unsignedInteger('quantity')->default(0);

            // Add foreign key constraint with ON DELETE CASCADE
            $table->foreign('order_payment_id')
                ->references('id')->on('order_payments')
                ->onDelete('cascade'); // This line enables cascade deleting
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_orders');
    }
}
