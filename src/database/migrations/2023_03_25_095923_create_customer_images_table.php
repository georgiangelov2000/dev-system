<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_images', function (Blueprint $table) {
            $table->foreignId('customer_id')
                    ->constrained('customers')
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
    public function down()
    {
        Schema::dropIfExists('customer_images');
    }
}
