<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCallLogTimelinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('call_log_timelines', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_data_id')->nullable();
            $table->unsignedBigInteger('call_log_payment_id')->nullable();
            $table->unsignedBigInteger('identified_by_id')->nullable();
            $table->string('identified_by')->nullable();
            $table->string('date_uploaded')->nullable();
            $table->string('date_identified')->nullable();
            $table->string('date_reviewed')->nullable();
            $table->double('payment_amount')->nullable();
            $table->string('payment_notification')->nullable();
            $table->string('payment_by')->nullable();
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
        Schema::dropIfExists('call_log_timelines');
    }
}
