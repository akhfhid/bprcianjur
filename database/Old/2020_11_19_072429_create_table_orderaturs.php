<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableOrderaturs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orderaturs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('peraturan_id')->unsigned();
            $table->integer('pegawai_id')->unsigned();
            $table->integer('cabang_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->string('ket');
            $table->enum('status',['SUBMIT','PROCESS','FINISH','CANCEL']);
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
        Schema::dropIfExists('orderaturs');
    }
}
