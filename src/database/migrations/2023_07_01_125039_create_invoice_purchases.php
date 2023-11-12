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
            $table->unsignedBigInteger('purchase_payment_id')->index();
            $table->string('invoice_number',20)->nullable()->unique();
            $table->date('invoice_date')->nullable();
            $table->unsignedDecimal('price',8,2)->default(0);
            $table->unsignedInteger('quantity')->default(0);

            // Add foreign key constraint with ON DELETE CASCADE
            $table->foreign('purchase_payment_id')
                ->references('id')->on('purchase_payments')
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
        Schema::dropIfExists('invoice_purchases');
    }
}
