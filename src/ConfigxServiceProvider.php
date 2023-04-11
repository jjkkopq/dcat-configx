<?php

namespace Jjkkopq\DcatConfigx;

use Dcat\Admin\Extend\ServiceProvider;
use Jjkkopq\DcatConfigx\Http\Middleware\AfterInjectDcatConfigx;
use Jjkkopq\DcatConfigx\Http\Middleware\BeforeInjectDcatConfigx;
use Jjkkopq\DcatConfigx\Http\Middleware\MiddleInjectDcatConfigx;

class ConfigxServiceProvider extends ServiceProvider
{
    protected $menu = [
        [
            'title' => 'dcat-configx',
            'uri'   => '',
            'icon'  => 'fa-sliders', // 图标可以留空
        ],
        [
            'parent'=> 'dcat-configx',
            'title' => 'Web Configx',
            'uri'   => 'dcat-configx/configx',
            'icon'  => 'fa-gear',
        ],
        [
            'parent'=> 'dcat-configx',
            'title' => 'Web Setting',
            'uri'   => 'dcat-configx/admin',
            'icon'  => 'fa-gear',
        ],
    ];

    protected $middleware = [
        'before' => [
            BeforeInjectDcatConfigx::class,
        ],
        'middle' => [
            MiddleInjectDcatConfigx::class,
        ],
        'after' => [
            AfterInjectDcatConfigx::class,
        ]
    ];
	// public function init()
	// {
	// 	parent::init();
	// }
}
