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
            $table->integer('quantity');
            $table->text('notes')->nullable();
            $table->text('code');
            $table->integer('price');
            $table->string('stock_keeping_unit');
            $table->string('lot_number');
            $table->enum('new', ['yes', 'no']);

            $table->foreignId('category_id')
                    ->constrained('categories')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->foreignId('brand_id')
                    ->constrained('brands')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->foreignId('unit_id')
                    ->constrained('units')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->foreignId('image_id')
                    ->constrained('images')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
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
