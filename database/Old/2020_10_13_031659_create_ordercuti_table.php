<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdercutiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordercuti', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->float('jmlcuti')->unsigned()->default(0);
            $table->date('tglawal');
            $table->date('tglakhir');
            $table->string('alasan');
            $table->enum('status',['SUBMIT','PROSES','DITOLAK','DISETUJUI']);
            $table->integer('cabang');
            $table->timestamps();
            //$table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ordercuti',function(Blueprint $table){
            $table->dropforeign('ordercuti_user_id_foreign');
        });
        Schema::dropIfExists('ordercuti');
    }
}
