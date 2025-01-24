<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_request', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->date('date_request');
            $table->string('divisi');
            $table->string('no_pr')->unique();
            $table->string('pt');
            $table->string('important');
            $table->integer('status');
            $table->string('signature');
            $table->string('acc_sign')->nullable();
            $table->string('acc_by')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('ID')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_request');
    }
}
