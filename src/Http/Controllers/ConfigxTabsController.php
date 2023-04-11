<?php

namespace Jjkkopq\DcatConfigx\Http\Controllers;

use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Illuminate\Support\Str;
use Dcat\Admin\Layout\Content;
use Jjkkopq\DcatConfigx\Support;
use Jjkkopq\DcatConfigx\Actions\CreateButton;
use Dcat\Admin\Http\Controllers\AdminController;
use Jjkkopq\DcatConfigx\Models\ConfigxTabsModel as Model;

class ConfigxTabsController extends AdminController
{
    public function title()
    {
        return Support::trans('configx.tabs');
    }

    public function index(Content $content)
    {
        return $content
            ->title($this->title())
            ->body($this->grid());
        // ->full();
    }

    protected function grid()
    {
        return Grid::make(new Model(), function (Grid $grid) {

            $grid->disableColumnSelector();
            $grid->disablePagination();
            $grid->disableRowSelector();
            $grid->disableRefreshButton();
            $grid->disableEditButton();
            $grid->disableViewButton();
            $grid->disableCreateButton();
            // $grid->enableDialogCreate();
            $grid->disableFilterButton();
            // $grid->filter(function ($filter) {
            //     $filter->disableIdFilter();
            // });

            $grid->tools(new CreateButton([
                'class' => 'btn-default',
                'icon' => 'fa-list',
                'url' => admin_route('dcat-configx.admin.index'),
                'name' => Support::trans('configx.title'),
                // 'target' => '_blank'
            ]));


            $grid->quickCreate(function (Grid\Tools\QuickCreate $create) {
                $create->text('slug', Support::trans('configx.slug'));
                $create->text('name', Support::trans('configx.name'));
            });
            // $grid->id('ID')->sortable();
            $grid->column('slug', Support::trans('configx.slug'));
            $grid->column('name', Support::trans('configx.name'))->editable();
        });
    }

    protected function form()
    {
        return Form::make(new Model(), function (Form $form) {
            // $form->hidden('id', 'ID');
            $table = (new Model)->getTable();
            $connection = config('admin.database.connection') ?? config('database.connection');
            $id = $form->getKey();
            $form->text('slug', Support::trans('configx.slug'))
                ->required()
                ->creationRules(['required', "unique:{$connection}.{$table}"])
                ->updateRules(['required', "unique:{$connection}.{$table},slug,$id"]);
            $form->text('name', Support::trans('configx.name'))->rules('required');
        })->saving(function (Form $form) {
            if (in_array($form->slug, ['app', 'admin', 'auth', 'keys', 'api', 'cache', 'captcha', 'broadcasting', 'aliyun', 'cors', 'database', 'debugbar', 'echarts', 'excel', 'filesystems', 'hashing', 'image', 'jwt', 'laravels', 'logging', 'mail', 'octane', 'permission', 'queue', 'sanctum', 'services', 'session', 'sitemap', 'telescope', 'ueditor', 'view', 'wechat'])) {
                // 中断后续逻辑
                return $form->response()->error('系统配置名，无法设置');
            }
        });
    }
}
