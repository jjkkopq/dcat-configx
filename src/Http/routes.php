<?php

use Illuminate\Support\Facades\Route;
use Jjkkopq\DcatConfigx\Http\Controllers;

Route::resource('dcat-configx/configx', Controllers\ConfigxController::class)->names('dcat-configx');
Route::resource('dcat-configx/configx_tabs', Controllers\ConfigxTabsController::class)->names('dcat-configx.tabs');

Route::get('dcat-configx/admin', [Controllers\AdminController::class, 'index'])
    ->name('dcat-configx.admin.index');
