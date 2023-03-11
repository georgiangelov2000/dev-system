<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('supplier_id')
                    ->constrained('suppliers')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->integer('quantity')->default(0);
            $table->text('notes')->nullable();
            $table->decimal('price', 8, 2);
            $table->integer('discount_price')->nullable();
            $table->decimal('discount_percent', 8, 2)->nullable();
            $table->string('code',20);
            $table->date('start_date_discount');
            $table->date('end_date_discount');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('products');
    }

}
