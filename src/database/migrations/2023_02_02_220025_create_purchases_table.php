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
            $table->foreignId('supplier_id')
                    ->constrained('suppliers')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedDecimal('price', 8, 2)->default(0);
            $table->unsignedDecimal('total_price', 8, 2)->default(0);
            $table->unsignedInteger('initial_quantity')->default(0);
            $table->text('notes')->nullable();
            $table->string('code',20);
            $table->enum('status', ['enabled', 'disabled']);
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
