<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePegawaiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pegawai', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('NIK Kepegawaian');
            $table->string('templahir');
            $table->date('tgllahir');
            $table->enum('kelamin',['Laki-Laki','Perempuan']);
            $table->text('alamat');
            $table->string('agama');
            $table->enum('status',['Kawin', 'Belum Kawin']);
            $table->string('pendidikan');
            $table->date('tglmasuk');
            $table->integer('created_by');
            $table->integer('update_by')->nullable();
            $table->integer('deleted_by')->nullable();
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
        Schema::dropIfExists('pegawai');
    }
}
