<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePegawaiOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pegawai_cuti', function (Blueprint $table) {
            $table->increment('id');
            $table->integer('order_id');
            $table->integer('pegawai_id');
            $table->integer('jmlcuti');
            $table->date('tglmulai');
            $table->date('tglakhir');
            $table->string('alasan');
            $table->string('status');

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
        Schema::dropIfExists('pegawai_order');
    }
}
