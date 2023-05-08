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
            $table->foreignId('customer_id')
                ->constrained('customers')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('product_id')
                ->constrained('products')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('package_id')
                ->constrained('packages')
                ->onUpdate('cascade')
                ->onDelete('cascade');    

            $table->string('invoice_number');
            $table->integer('sold_quantity');
            $table->decimal('single_sold_price', 8, 2);
            $table->decimal('total_sold_price', 8, 2);
            $table->integer('discount_percent');
            $table->date('date_of_sale');
            $table->string('tracking_number');
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
