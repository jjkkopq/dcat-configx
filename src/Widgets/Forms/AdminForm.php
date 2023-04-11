<?php

namespace Jjkkopq\DcatConfigx\Widgets\Forms;

use Illuminate\Support\Arr;
use Dcat\Admin\Widgets\Form;
use Jjkkopq\DcatConfigx\Configx;
use \Jjkkopq\DcatConfigx\Support;
use Dcat\Admin\Extend\ServiceProvider;
use Jjkkopq\DcatConfigx\Models\ConfigxModel;
use Jjkkopq\DcatConfigx\ConfigxServiceProvider;

class AdminForm extends Form
{
    /**
     * Handle the form request.
     *
     * @param array $input
     *
     * @return mixed
     */
    public function handle(array $input)
    {
        $input['layout']['horizontal_menu'] = in_array('horizontal_menu', $input['layout']['body_class'], true);
        $input['layout']['body_class'] = json_encode($input['layout']['body_class']);
        $configs = Arr::dot(['admin' => $input]);

        foreach ($configs as $key => $value) {
            switch ($key) {
                case 'admin.logo':
                case 'admin.logo_mini':
                    $type = 'image';
                    break;
                case 'admin.layout.body_class':
                    $type = 'checkbox';
                    break;
                default:
                    $type = 'normal';
                    break;
            }
            $item = ConfigxModel::firstOrCreate([
                'type' => $type,
                'slug' => $key
            ]);
            $item->update(['value' => $value]);
        }

        return $this
            ->response()
            ->success('站点配置更新成功！')
            ->refresh();
    }

    /**
     * 主题颜色.
     *
     * @var array
     */
    protected $colors = [
        'default'    => '默认',
        'blue'       => '蓝',
        'blue-light' => '浅蓝',
        //        'blue-dark'  => '深蓝',
        'green'      => '绿',
    ];
    /**
     * Build a form here.
     */
    public function form()
    {
        $this->text('name', Support::trans('main.site_title'))
            ->default(Configx::lastVal('admin.name'));
        $this->text('title', Support::trans('main.site_title'));
        $this->image('logo', Support::trans('main.site_logo'))
            ->autoUpload()
            ->uniqueName();
        $this->image('logo_mini', Support::trans('main.site_logo_mini'))
            ->autoUpload()
            ->uniqueName();
        // $this->radio('lang', Support::trans('main.site_lang'))
        //     ->options([
        //         'zh_CN' => '中文（简体）',
        //         'en' => 'English'
        //     ]);
        // $this->switch('https', '启用HTTPS');
        // $this->switch('debug', Support::trans('main.site_debug'))
        // ->help('开启 debug 模式后将会显示异常捕获信息，关闭则只返回 500 状态码。');

        $this->uiForm();
    }
    public function uiForm()
    {
        $defaultColors = $this->colors;
        foreach (explode(",", ConfigxServiceProvider::setting('additional_theme_colors')) as $value) {
            if (!empty($value)) {
                [$k, $v] = explode(":", $value);
                $defaultColors[$k] = $v;
            }
        }
        $this->radio('layout.color', Support::trans('main.theme_color'))
            ->required()
            ->options($defaultColors)
            ->help('主题颜色，支持自定义！');

        $this->radio('layout.sidebar_style', Support::trans('main.sidebar_style'))
            ->options(['light' => 'Light', 'primary' => 'Primary', 'dark' => 'Dark'])
            ->help('切换菜单栏样式');

        $this->radio('layout.navbar_color', Support::trans('main.navbar_color'))
            ->options(['' => 'None', 'bg-primary' => 'Primary', 'bg-info' => 'Info',  'bg-warning' => 'Warning', 'bg-success' => 'Success', 'bg-danger' => 'Danger', 'bg-dark' => 'Dark'])
            ->help('切换导航栏主题');

        $this->checkbox('layout.body_class', Support::trans('main.body_class'))
            ->options([
                'horizontal_menu' => '水平 (Horizontal)',
                'sidebar-separate' => '分离 (sidebar-separate)',
            ])
            ->help('切换菜单布局');

        // $this->switch('layout.grid_row_actions_right', Support::trans('main.grid_row_actions_right'))
        //     ->help('启用后表格行操作按钮将永远贴着最右侧。');

        // $this->switch('layout.footer_remove', Support::trans('main.footer_remove'));

        // $this->switch('helpers.enable', '开发工具');
    }

    function default()
    {
        return [
            'name' => Configx::lastVal('admin.name'),
            'title' => Configx::lastVal('admin.title'),
            'logo' => Configx::val('admin.logo', ''),
            'logo_mini' => Configx::val('admin.logo_mini', ''),
            'lang' => Configx::val('admin.lang'),
            'https' => Configx::lastVal('admin.https'),
            'debug' => Configx::val('admin.debug'),
            'layout.color' => Configx::lastVal('admin.layout.color'),
            'layout.sidebar_style' => Configx::lastVal('admin.layout.sidebar_style'),
            'layout.navbar_color' => Configx::lastVal('admin.layout.navbar_color'),
            'layout.body_class' => Configx::lastVal('admin.layout.body_class'),
            'layout.grid_row_actions_right' => Configx::val('admin.layout.grid_row_actions_right'),
            'layout.footer_remove' => Configx::val('admin.layout.footer_remove'),
            'helpers.enable' => Configx::lastVal('admin.helpers.enable'),
        ];
    }
}
