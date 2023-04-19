<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMan3000AreaCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('man3000_area_codes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("user_data_id");
            $table->string('area_code');
            $table->double('total_monthly_cost')->nullable();
            // $table->double('total_monthly_cost');
            $table->double('identified_amount')->nullable();  
            $table->double('reviewed_amount')->nullable(); 
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
        Schema::dropIfExists('man3000_area_codes');
    }
}
