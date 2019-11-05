<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			$table->string('img')->nullable();
			$table->string('national_id', 17)->nullable();
			$table->string('contact_no', 11)->nullable();
			$table->integer('country_id')->nullable();
			$table->integer('city_id')->nullable();
			$table->text('present_address')->nullable();
			$table->text('permanent_address')->nullable();
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
        Schema::dropIfExists('profiles');
    }
}
