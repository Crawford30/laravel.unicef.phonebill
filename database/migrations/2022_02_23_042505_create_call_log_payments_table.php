<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCallLogPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('call_log_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('bill_owner_id');
            $table->unsignedBigInteger("user_data_id");
            $table->integer('personal_calls_count')->nullable();
            $table->integer('official_calls_count')->nullable();
            $table->string('bill_type')->nullable();
            $table->double('identified_amount')->nullable();
            $table->double('reviewed_amount')->nullable(); 
            $table->string('reviewed_by')->nullable(); 
            $table->string('status', 10)->default("Pending");
            $table->string('call_log_for')->nullable(); 
            $table->string('section')->nullable();
            $table->string('from_date')->nullable();
            $table->string('to_date')->nullable();
            $table->string('document_one')->nullable();
            $table->string('document_two')->nullable();
            $table->string('invoice_status', 255)->default("NotSettled");
            
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
        Schema::dropIfExists('call_log_payments');
    }
}
