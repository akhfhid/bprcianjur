<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTgChatIdToPegawaisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('pegawais', 'tg_chat_id')) {
            Schema::table('pegawais', function (Blueprint $table) {
                $table->string('tg_chat_id', 64)->nullable()->after('nohp');
                $table->index('tg_chat_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('pegawais', 'tg_chat_id')) {
            Schema::table('pegawais', function (Blueprint $table) {
                $table->dropIndex(['tg_chat_id']);
                $table->dropColumn('tg_chat_id');
            });
        }
    }
}
