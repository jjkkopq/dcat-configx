<?php

namespace Jjkkopq\DcatConfigx\Widgets\Forms;

use Illuminate\Support\Arr;
use Dcat\Admin\Widgets\Form;
use Jjkkopq\DcatConfigx\Configx;
use Jjkkopq\DcatConfigx\Support;
use Dcat\Admin\Traits\LazyWidget;
use Jjkkopq\DcatConfigx\UserAdmin;
use Dcat\Admin\Contracts\LazyRenderable;
use Jjkkopq\DcatConfigx\ConfigxServiceProvider;

class UserAdminForm extends Form implements LazyRenderable
{
    use LazyWidget;

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
     * 处理表单请求.
     *
     * @param array $input
     *
     * @return mixed
     */
    public function handle(array $input)
    {
        $input['layout']['horizontal_menu'] = in_array('horizontal_menu', $input['layout']['body_class'], true);
        if ($input['layout']['clear_session']) {
            UserAdmin::configClear();
        } else {
            // dd($input);
            UserAdmin::config(Arr::dot(['admin' => $input]));
        }


        return $this->response()->success('设置成功');
    }

    /**
     * 构建表单.
     */
    public function form()
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
        $this->switch('layout.clear_session', Support::trans('main.clear_session'))
            ->default(0);

        // $this->switch('helpers.enable', '开发工具');
    }

    /**
     * 设置接口保存成功后的回调JS代码.
     *
     * 1.2秒后刷新整个页面.
     *
     * @return string|void
     */
    public function savedScript()
    {
        return <<<'JS'
    if (data.status) {
        setTimeout(function () {
            location.reload()
        }, 1200);
    }
JS;
    }

    /**
     * 返回表单数据.
     *
     * @return array
     */
    public function default()
    {
        $default = UserAdmin::config();
        if (count($default) == 0) {
            $default = Configx::lastVal('admin');
        }
        return $default;
    }
}
