<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccessManagementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('access_management', function (Blueprint $table) {
            $table->id();
            $table->string('access');
            $table->integer('identity');
        });
    }

    /**
     * Reverse the migrations.
     *s
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('access_management');
    }
}
