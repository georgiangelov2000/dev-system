<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsSoldTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('products_sold', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')
                    ->constrained('customers')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            
            $table->foreignId('product_id')
                    ->constrained('products')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            
            $table->integer('sold_quantity');
            $table->date('date_of_sale');
            $table->decimal('price', 8, 2);
            $table->decimal('discount_price', 8, 2);
            $table->integer('discount_percent');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('products_sold');
    }

}
