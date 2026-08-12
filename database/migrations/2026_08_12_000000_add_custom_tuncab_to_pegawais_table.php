<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomTuncabToPegawaisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pegawais', function (Blueprint $table) {
            if (!Schema::hasColumn('pegawais', 'is_custom_tuncab')) {
                $table->boolean('is_custom_tuncab')->default(0)->after('tuncab');
            }
            if (!Schema::hasColumn('pegawais', 'custom_tuncab_val')) {
                $table->decimal('custom_tuncab_val', 8, 4)->nullable()->after('is_custom_tuncab');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pegawais', function (Blueprint $table) {
            if (Schema::hasColumn('pegawais', 'custom_tuncab_val')) {
                $table->dropColumn('custom_tuncab_val');
            }
            if (Schema::hasColumn('pegawais', 'is_custom_tuncab')) {
                $table->dropColumn('is_custom_tuncab');
            }
        });
    }
}
