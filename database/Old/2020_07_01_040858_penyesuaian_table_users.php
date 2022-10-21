<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PenyesuaianTableUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table){
            $table->string("username")->unique();
            $table->string("roles");
            $table->text("address");
            $table->string("phone");
            $table->string("avatar");
            $table->enum("status",["ACTIVE","INACTIVE"]);
            $table->string("name");
            $table->string("tlahir");
            $table->date("tgllahir");
            $table->string("skawin");
            $table->string("spegawai");
            $table->string("golongan");
            $table->string("jabatan");
            $table->string("nik")->unique();
            $table->date("tglmasuk");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users',function (Blueprint $table){
            $table->dropcolumn("username");
            $table->dropcolumn("roles");
            $table->dropcolumn("address");
            $table->dropcolumn("phone");
            $table->dropcolumn("avatar");
            $table->dropcolumn("status");

        });
    }
}
