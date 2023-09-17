<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('package_name');
            $table->string('tracking_number')->unique();
            $table->tinyInteger('package_type')->comment('1: Standard, 2: Express, 3: Overnight');
            $table->tinyInteger('delivery_method')->comment("1: Ground, 2: Air, 3: Sea");
            $table->date('delivery_date')->nullable();
            $table->date('expected_delivery_date')->nullable();
            $table->tinyInteger('is_it_delivered')->comment('1: Delivered, 0: Not delivered')->default(0);
            $table->string('package_notes')->default('');
            $table->string('customer_notes')->default('');
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
        Schema::dropIfExists('packages');
    }
}
