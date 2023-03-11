<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProductImages extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->string('path');
            $table->string('name');
            $table->foreignId('product_id')
                    ->constrained('products')
                    ->onUpdate('cascade')
                    ->onDelete('cascade')
                    ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('product_images');
    }

}
