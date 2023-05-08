<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerIdPriceToCustomerPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_payments', function (Blueprint $table) {
            $table->decimal('price',8,2);
            $table->foreignId('customer_id')
            ->constrained('customers')
            ->onUpdate('cascade')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_payments', function (Blueprint $table) {
            Schema::dropIfExists('customer_id');
        });
    }
}
