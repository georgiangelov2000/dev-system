<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagesOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')
            ->constrained('packages')
            ->onUpdate('cascade')
            ->onDelete('cascade');
            $table->foreignId('order_id')
            ->constrained('orders')
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
    public function down()
    {
        Schema::dropIfExists('packages_orders');
    }
}
