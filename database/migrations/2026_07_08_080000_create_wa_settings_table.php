<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('wa_settings', function (Blueprint $table) {
            $table->id();
            $table->text('cabang_order')->nullable()->comment('JSON array of cabang IDs in blast priority order');
            $table->integer('delay_per_person')->default(10)->comment('Delay in seconds between each person');
            $table->integer('delay_per_cabang')->default(0)->comment('Additional delay in seconds between each branch');
            $table->timestamps();
        });

        // Insert default single row
        \DB::table('wa_settings')->insert([
            'cabang_order' => null,
            'delay_per_person' => (int) env('WA_THROTTLE_SECONDS', 10),
            'delay_per_cabang' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('wa_settings');
    }
}
