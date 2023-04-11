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
        if (! Schema::connection($this->getConnection())->hasTable('admin_configx')) {
            Schema::connection($this->getConnection())->create('admin_configx', function (Blueprint $table) {
                $table->bigIncrements('id')->unsigned();
                $table->string('slug')->comment('变量名')->unique();
                $table->string('name')->comment('变量名称');
                $table->string('type')->default('normal')->comment('类型');
                $table->text('value')->nullable()->comment('键值');
                $table->text('default_value')->nullable()->comment('默认键值');
                $table->text('option')->comment('选项:select,radio等选择类型的数据')->nullable();
                $table->text('description')->comment('说明')->nullable();
                $table->unsignedSmallInteger('sort')->comment('排序')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::connection($this->getConnection())->dropIfExists('admin_configx');
    }
}
