<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('value');
            $table->integer('payer')->unsigned();
            $table->integer('payee')->unsigned();

            $table->foreign('payer')->references('id')->on('users');
            $table->foreign('payee')->references('id')->on('users');
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
        Schema::table('transactions', function (Blueprint $table){
            $table->dropForeign('payer');
            $table->dropForeign('payee');
        });
        Schema::dropIfExists('transactions');
    }
}
