<?php

namespace Jjkkopq\DcatConfigx;

use Dcat\Admin\Admin;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Jjkkopq\DcatConfigx\Models\ConfigxModel;

class Configx
{
    public static function load()
    {
        config(self::getDataDot());
    }
    /**
     * 获取或保存配置参数.
     *
     * @param  string|array  $key
     * @param  mixed  $default
     * @return \Jjkkopq\DcatConfigx\Models\ConfigxModel|mixed
     */
    public static function val($key = null, $default = null)
    {
        if ($key === null) {
            return (new ConfigxModel);
        }

        // 缓存取值
        if (is_array($key)) {
            return;
        }
        $configs = self::getDataDot();
        return Arr::get($configs, $key, $default);
    }
    public static function lastVal($key = null, $default = null)
    {
        return self::val($key, $default) ?: config($key, $default);
    }
    protected static function getDataDot()
    {
        return Arr::dot((new static)->getData());
    }

    public static function getData()
    {
        if (Admin::extension()->enabled('jjkkopq.dcat-configx')) {
            $configs = (new ConfigxModel)->allNodes()->map(function ($v) {
                return ConfigxTool::formatValue($v);
            });
            // 按数据类型保存 configx开启后
            return $configs->pluck('value', 'slug');
        }
        return [];
    }

    /**
     * Group configs by prefix
     *
     * @param [string] $prefix
     * @return Collect
     */
    public static function group($prefixs)
    {
        $prefixs = is_array($prefixs) ? $prefixs : [$prefixs];
        foreach ($prefixs as $key => $prefix) {
            $prefixs[$key] = $prefix . '.';
        }
        return (new ConfigxModel)->allNodes()->filter(function ($v) use ($prefixs) {
            return Str::contains($v->slug, $prefixs);
        })->sortBy('sort')->map(function ($v) {
            return ConfigxTool::formatValue($v);
        });
    }
}
