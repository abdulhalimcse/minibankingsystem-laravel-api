<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			$table->string('from_account_no');
			$table->string('to_account_no')->nullable();
			$table->double('balance', 8, 2)->default(0);
			$table->double('deposit', 8, 2)->default(0);
			$table->double('withdraw', 8, 2)->default(0);
			$table->double('current_balance', 8, 2)->default(0);
            $table->tinyInteger('status')->comment('0=Pending, 1=Delete, 2=Inactive, 3=Active')->default(0);
            $table->integer('created_by')->nullable();
            $table->integer('modified_by')->nullable();
            $table->timestamps();
			$table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
