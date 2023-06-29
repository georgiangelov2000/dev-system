<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreSupplierPaymentColumnsToSupplierPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('supplier_payments', function (Blueprint $table) {
            $table->tinyInteger('payment_method')->default(0)->comment('1: Cash, 2: Bank Transfer, 3: Credit Card, 4: Cheque, 5: Online Payment');
            $table->string('payment_reference')->default('N/A');
            $table->tinyInteger('payment_status')->default(0)->comment('1: Pending, 2: Paid, 3: Partially Paid, 4: Overdue, 5: Refunded, 6: Other');
            $table->string('notes')->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('supplier_payments', function (Blueprint $table) {
            $table->dropColumn('payment_method');
            $table->dropColumn('payment_reference');
            $table->dropColumn('payment_status');
            $table->dropColumn('notes');
        });
    }
}
