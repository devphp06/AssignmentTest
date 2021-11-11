<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPinVerficationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_pin_verfications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('send_pin');
            $table->unsignedBigInteger('user_email_verifications_id');
            $table->foreign('user_email_verifications_id')->references('id')->on('user_email_verifications')->onDelete('cascade');
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
        Schema::dropIfExists('user_pin_verfications');
    }
}
