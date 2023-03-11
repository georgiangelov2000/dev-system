<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SupplierImages extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('supplier_images', function (Blueprint $table) {

            $table->id();
            $table->foreignId('supplier_id')
                    ->constrained('suppliers')
                    ->onUpdate('cascade')
                    ->onDelete('cascade')
                    ->nullable();
            $table->string('path');
            $table->string('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('supplier_images');
    }

}
