<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('address');
            $table->string('zip');
            $table->string('website');
            $table->text('notes')->nullable();

            $table->foreignId('state_id')
                    ->constrained('states')
                    ->onUpdate('cascade')
                    ->onDelete('cascade')
                    ->nullable();
            
            $table->foreignId('country_id')
                    ->constrained('countries')
                    ->onUpdate('cascade')
                    ->onDelete('cascade')
                    ->nullable();
            
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
        Schema::dropIfExists('customers');
    }
}
