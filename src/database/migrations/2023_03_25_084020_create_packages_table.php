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
            $table->string('tracking_number');
            $table->tinyInteger('package_type')->comment('1=Standart,2=Express,3=Overnight');
            $table->tinyInteger('delievery_method')->comment("1=Ground,2=Air,3=Sea");
            $table->date('delievery_date')->nullable();
            $table->string('package_notes')->nullable();
            $table->string('customer_notes')->nullable();
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
