<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJenisLpsToPeraturansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
{
    Schema::table('peraturans', function (Blueprint $table) {
        $table->string('jenis_lps')->nullable()->after('jenis_ojk');
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
