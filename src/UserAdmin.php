<?php

namespace Jjkkopq\DcatConfigx;

use Dcat\Admin\Admin;
use Illuminate\Support\Arr;

class UserAdmin
{
    public static function loadNavbar($navbar)
    {
        if (!\Dcat\Admin\Support\Helper::isAjaxRequest()) {
            if (Admin::extension()->enabled('jjkkopq.dcat-configx')) {
                $method = config('admin.layout.horizontal_menu') ? 'left' : 'right';
                $navbar->$method(\Jjkkopq\DcatConfigx\Actions\AdminSetting::make()->render());
            }
        }
    }
    public static function config($key = null, $default = null)
    {
        $session = session();

        // 未设置获取初始配置项
        if (!$config = $session->get('user_admin.config')) {
            $config = [];
        }

        if (is_array($key)) {
            // 保存
            foreach ($key as $k => $v) {
                Arr::set($config, $k, $v);
            }

            $session->put('user_admin.config', $config);

            return;
        }

        if ($key === null) {
            return Arr::dot($config);
        }

        return Arr::get($config, $key, $default ?? Configx::lastVal($key));
    }
    public static function configClear()
    {
        $session = session();

        // 未设置获取初始配置项
        if ($session->get('user_admin.config')) {
            $session->forget('user_admin.config');
        }
    }
}
