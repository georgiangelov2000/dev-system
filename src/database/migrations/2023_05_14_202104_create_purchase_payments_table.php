<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasePaymentsTable extends Migration
{
    /** 
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_id')->index();
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedDecimal('price', 8, 2)->default(0);
            $table->tinyInteger('payment_method')->nullable()->comment('1: Cash, 2: Bank Transfer, 3: Credit Card, 4: Cheque, 5: Online Payment');
            $table->string('payment_reference')->default('N/A');
            $table->tinyInteger('payment_status')->default(2)->comment('1:Successfully_Paid_Delivered, 2: Pending, 4: Overdue');
            $table->tinyInteger('delivery_status')->default(2)->comment('1:Successfully_Paid_Delivered, 2: Pending, 4: Overdue');
            $table->date('date_of_payment')->nullable();
            $table->date('expected_date_of_payment');

            // Add foreign key constraint with ON DELETE CASCADE
            $table->foreign('purchase_id')
                ->references('id')->on('purchases')
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
        Schema::dropIfExists('purchase_payments');
    }
}
