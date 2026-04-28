<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EnsureSoftDeleteColumnsOnPeraturansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->ensureColumns('peraturans');
        $this->ensureColumns('peraturan');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }

    /**
     * Ensure peraturan tables support Laravel soft deletes.
     *
     * @param  string  $tableName
     * @return void
     */
    protected function ensureColumns($tableName)
    {
        if (!Schema::hasTable($tableName)) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($tableName) {
            if (!Schema::hasColumn($tableName, 'deleted_by')) {
                $table->unsignedInteger('deleted_by')->nullable();
            }

            if (!Schema::hasColumn($tableName, 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }
}
