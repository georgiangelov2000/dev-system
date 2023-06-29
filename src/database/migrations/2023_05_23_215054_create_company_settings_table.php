<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanySettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('name');
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
            $table->string('phone_number');
            $table->string('tax_number');
            $table->string('address');
            $table->string('website');
            $table->string('owner_name');
            $table->string('tax_id');
            $table->string('bussines_type');
            $table->date('registration_date');
            $table->string('image_path');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_settings');
    }
}
