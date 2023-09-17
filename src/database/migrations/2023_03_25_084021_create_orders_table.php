<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('purchase_id');
            $table->unsignedInteger('sold_quantity')->default(0);
            $table->unsignedDecimal('single_sold_price', 8, 2)->default(0);
            $table->unsignedDecimal('discount_single_sold_price', 8, 2)->default(0);
            $table->unsignedDecimal('total_sold_price', 8, 2)->default(0);
            $table->unsignedDecimal('original_sold_price', 8, 2)->default(0);
            $table->unsignedInteger('discount_percent')->default(0);
            $table->date('package_extension_date')->nullable();
            $table->date('date_of_sale');
            $table->string('tracking_number');
            $table->tinyInteger('status')->comment('1: Paid, 2: Pending, 3: Partially Paid, 4: Overdue, 5: Refunded, 6: Ordered');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
