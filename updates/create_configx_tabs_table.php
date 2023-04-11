<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOperationLogTable extends Migration
{
    public function getConnection()
    {
        return config('admin.database.connection') ?: config('database.default');
    }

    public function up()
    {
        if (! Schema::connection($this->getConnection())->hasTable('admin_configx_tabs')) {
            Schema::connection($this->getConnection())->create('admin_configx_tabs', function (Blueprint $table) {
                $table->bigIncrements('id')->unsigned();
                $table->string('slug')->comment('识别')->unique();
                $table->string('name')->comment('名称')->unique();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::connection($this->getConnection())->dropIfExists('admin_configx_tabs');
    }
}
