<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedDecimal('price', 8, 2)->default(0);
            $table->unsignedDecimal('discount_price', 8, 2)->default(0);
            $table->unsignedDecimal('total_price', 8, 2)->default(0);
            $table->unsignedDecimal('original_price',8,2)->default(0);
            $table->date('delivery_date')->nullable();
            $table->date('expected_delivery_date');
            $table->unsignedInteger('discount_percent')->default(0);
            $table->unsignedInteger('initial_quantity')->default(0);
            $table->string('notes')->default('');
            $table->string('code',20);
            $table->string('image_path')->nullable();
            $table->tinyInteger('is_it_delivered')->comment('1: Delivered, 0: Not delivered')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('purchases');
    }

}
