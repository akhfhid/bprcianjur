<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRelasiPegawaiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('relasi_pegawai', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pegawai_id')->unsigned()->nullable();
            $table->integer('cabang_id')->unsigned()->nullable();
            $table->integer('jabatan_id')->unsigned()->nullable();
            $table->integer('pangkat_id')->unsigned()->nullable();            
            $table->timestamps();

            $table->foreign('pegawai_id')->references('id')->on('pegawai');
            $table->foreign('cabang_id')->references('id')->on('cabangs');
            $table->foreign('jabatan_id')->references('id')->on('jabatans');
            $table->foreign('pangkat_id')->references('id')->on('pangkats');          
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('relasi_pegawai');
    }
}
