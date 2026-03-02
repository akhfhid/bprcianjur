<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKategoriDanJenisToPeraturansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('peraturans', function (Blueprint $table) {
            $table->enum('kategori', ['internal', 'external'])->after('name');
            $table->enum('jenis_surat', ['SK', 'SE'])->after('kategori');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('peraturans', function (Blueprint $table) {
            //
        });
    }
}
