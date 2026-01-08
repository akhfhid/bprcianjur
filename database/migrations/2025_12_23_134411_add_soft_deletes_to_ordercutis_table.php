<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftDeletesToOrdercutisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up()
{
    Schema::table('ordercutis', function (Blueprint $table) {
        $table->softDeletes(); 
        $table->text('alasan_hapus')->nullable()->after('status'); 
    });
}

public function down()
{
    Schema::table('ordercutis', function (Blueprint $table) {
        $table->dropSoftDeletes();
        $table->dropColumn('alasan_hapus');
    });
}
}
