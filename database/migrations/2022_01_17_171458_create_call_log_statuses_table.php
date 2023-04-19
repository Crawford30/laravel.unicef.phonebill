<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCallLogStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('call_log_statuses', function (Blueprint $table) {
          
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_data_id')->nullable();
            $table->unsignedBigInteger('bill_owner_id')->nullable();
            $table->string('call_log_with')->nullable();
            $table->integer('personal_count')->nullable();
            $table->integer('official_count')->nullable();
            $table->integer('total_count')->nullable();
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
        Schema::dropIfExists('call_log_statuses');
    }
}
