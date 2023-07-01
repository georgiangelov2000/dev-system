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
            $table->unsignedInteger('initial_quantity')->default(0);
            $table->string('notes')->nullable();
            $table->string('code',20);
            $table->tinyInteger('status')->comment('0=disabled, 1=enabled');
            $table->tinyInteger('is_paid')->comment('1=is_paid,0=not paid')->default(0);
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
