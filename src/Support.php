<?php

namespace Jjkkopq\DcatConfigx;

use Dcat\Admin\Admin;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class Support
{
    /**
     * 快速翻译（为了缩短代码量）
     * @param $string
     * @return array|string|null
     */
    public static function trans($string, $replace = [], $locale = null)
    {
        return Admin::extension()->get('jjkkopq.dcat-configx')->trans($string, $replace, $locale);
    }

    /**
     * 初始化配置注入
     */
    public function initConfig()
    {
        /**
         * 处理站点LOGO自定义
         */
        if (empty(Configx::val('admin.logo'))) {
            $logo = config('admin.logo');
        } else {
            $logo = Storage::disk(config('admin.upload.disk'))->url(Configx::val('site.logo'));
            $logo = "<img src='$logo' width='35'> &nbsp;" . Configx::lastVal('admin.name');
        }

        /**
         * 处理站点LOGO-MINI自定义
         */
        if (empty(Configx::val('admin.logo_mini'))) {
            $logo_mini = config('admin.logo-mini');
        } else {
            $logo_mini = Storage::disk(config('admin.upload.disk'))->url(Configx::val('admin.logo_mini'));
            $logo_mini = "<img src='$logo_mini'>";
        }

        /**
         * 复写admin站点配置
         */
        Configx::load();
        config([
            'admin.logo' => $logo,
            'admin.logo-mini' => $logo_mini,
        ]);
        // dd(UserAdmin::config());
        config(UserAdmin::config());
    }

    /**
     * 注入先后问题，未实现调用前覆盖
     */
    public static function initLang()
    {
        // dd(Admin::extension()->enabled('jjkkopq.dcat-configx'));
        if (Admin::extension()->enabled('jjkkopq.dcat-configx')) {
            /**
             * 复写app配置
             */
            $site_lang = Configx::val('admin.lang') ?: config('app.locale');
            $site_lang = UserAdmin::config('admin.lang') ?: $site_lang;
            // dd($site_lang);
            config([
                'app.locale' => $site_lang,
                'app.fallback_locale' => $site_lang,
            ]);
        }
    }

    /**
     * 注入字段.
     */
    public function injectFields()
    {
        \Jjkkopq\DcatConfigx\Form\Field\RadioMacro::macro();
    }

    /**
     * 底部授权移除.
     */
    public function footerRemove()
    {
        if (UserAdmin::config('admin.layout.footer_remove')) {
            Admin::style(
                <<<'CSS'
.main-footer {
    display: none;
}
CSS
            );
        }
    }

    /**
     * 行操作按钮最右.
     */
    public function gridRowActionsRight()
    {
        if (UserAdmin::config('admin.layout.grid_row_actions_right')) {
            Admin::style(
                <<<CSS
.grid__actions__{
    width: 20%;
    text-align: right;
}
CSS
            );
        }
    }
}
