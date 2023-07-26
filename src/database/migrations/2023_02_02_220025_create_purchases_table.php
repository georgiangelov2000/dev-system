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
            $table->unsignedDecimal('total_price', 8, 2)->default(0);
            $table->unsignedDecimal('original_price',8,2)->default(0);
            $table->date('expected_date_of_payment');
            $table->unsignedInteger('discount_percent')->default(0);
            $table->unsignedInteger('initial_quantity')->default(0);
            $table->string('notes')->default('');
            $table->string('code',20);
            $table->tinyInteger('status')->comment('1: Paid, 2: Pending, 3: Partially Paid, 4: Overdue, 5: Refunded')->nullable();
            $table->tinyInteger('is_paid')->comment('0: Not paid, 1: Paid, 2: Refund, 3: Partially Paid')->default(0);
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
