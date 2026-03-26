<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePeraturanViewSessionsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('peraturan_view_sessions')) {
            return;
        }

        Schema::create('peraturan_view_sessions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->unsignedBigInteger('pegawai_id')->nullable()->index();
            $table->unsignedBigInteger('peraturan_id')->index();
            $table->string('role', 30)->nullable()->index();
            $table->string('page_url')->nullable();
            $table->timestamp('started_at')->useCurrent()->index();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('ended_at')->nullable()->index();
            $table->unsignedInteger('active_seconds')->default(0);
            $table->unsignedInteger('idle_seconds')->default(0);
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['peraturan_id', 'started_at'], 'pvs_peraturan_started_idx');
            $table->index(['user_id', 'started_at'], 'pvs_user_started_idx');
        });
    }

    public function down()
    {
        if (Schema::hasTable('peraturan_view_sessions')) {
            Schema::drop('peraturan_view_sessions');
        }
    }
}
