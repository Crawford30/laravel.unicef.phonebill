<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhoneBillExtensionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    
        Schema::create('phone_bill_extensions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("user_id");
            $table->unsignedBigInteger("bill_owner_id")->nullable();
            $table->unsignedBigInteger("user_data_id");
            $table->string('ext');
            $table->string('area_code');
            $table->string('phone_number');
            $table->string('name')->nullable();
            $table->string('type');
            $table->string('date_time');
            $table->string('duration');
            $table->double('cost');
            $table->string('call_type')->nullable();
            $table->string('is_call_type_accepted')->nullable();
            $table->string('status', 10)->default("Pending");
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
        Schema::dropIfExists('phone_bill_extensions');
    }
}
