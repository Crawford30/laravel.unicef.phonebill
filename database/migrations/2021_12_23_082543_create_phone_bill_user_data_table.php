<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhoneBillUserDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('phone_bill_user_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('identification_deadline_date');
            $table->string('from_date');
            $table->string('to_date');
            $table->json("extensions");
            $table->double('total_monthly_cost');
            $table->integer('unique_mobile_number_count');
            $table->integer('unique_extensions_count');
            $table->integer('total_records');
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
        Schema::dropIfExists('phone_bill_user_data');
    }
}
