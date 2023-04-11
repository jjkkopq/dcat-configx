<?php

namespace Jjkkopq\DcatConfigx\Http\Controllers;

use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Illuminate\Support\Str;
use Dcat\Admin\Layout\Content;
use Jjkkopq\DcatConfigx\Configx;
use Jjkkopq\DcatConfigx\Support;
use Jjkkopq\DcatConfigx\ConfigxTool;
use Illuminate\Support\Facades\Request;
use Jjkkopq\DcatConfigx\Actions\CreateButton;
use Dcat\Admin\Http\Controllers\AdminController;
use Jjkkopq\DcatConfigx\Models\ConfigxTabsModel;
use Jjkkopq\DcatConfigx\Models\ConfigxModel as Model;

class ConfigxController extends AdminController
{
    public function title()
    {
        return Support::trans('configx.config');
    }

    public function index(Content $content)
    {
        return $content
            ->title($this->title())
            ->description('config("变量名") 获取变量名对应的值')
            ->body($this->grid());
    }

    protected function grid()
    {
        return Grid::make(new Model(), function (Grid $grid) {
            $items = Configx::group('admin');
            $grid->model()->whereNotIn('id', $items->pluck('id'));
            $grid->model()->orderByRaw('sort asc');
            // $grid->model()->orderByRaw('sort desc,id asc');
            $grid->disableRefreshButton();
            $grid->disableViewButton();
            $grid->enableDialogCreate();
            $grid->filter(function ($filter) {
                $filter->panel();
                //            $filter->expand(true);
                $filter->disableIdFilter();
                $filter->like('slug', '变量名');
                $filter->like('name', '名称');
                $filter->like('value', '搜索值');
                $filter->like('description', '描述');
            });
            $grid->tools(new CreateButton([
                'class' => 'btn-default',
                'icon' => 'fa-list',
                'url' => admin_route('dcat-configx.admin.index'),
                'name' => Support::trans('configx.title'),
                // 'target' => '_blank'
            ]));

            $grid->id('ID')->sortable();

            $grid->column('slug', '变量名')->copyable();
            $grid->column('name', '名称')->editable();
            $grid->column('value', '设置值')->display(function ($value, $column) {
                return ConfigxTool::gridValueCloumn($this, $value, $column);
            });
            $grid->column('default_value', '默认设置值')->display(function ($value, $column) {
                return ConfigxTool::gridValueCloumn($this, $value, $column);
            });
            // $grid->column('options', '变量值');
            // $grid->column('description', '说明')->editable();
            $grid->column('sort', '排序')->editable();
        });
    }

    protected function form()
    {
        return Form::make(new Model(), function (Form $form) {
            if ($form->isCreating()) {
                $form->ignore(['tab']);
                $form->radio('tab', Support::trans('configx.tabs'))->options(function () {
                    return (new ConfigxTabsModel)->allNodes()->pluck('name', 'slug');
                })->loadConfigxTabInfo();
            }
            $table = (new Model)->getTable();
            $connection = config('admin.database.connection') ?? config('database.connection');
            $id = $form->getKey();
            $form->text('slug', '变量名')
                ->required()
                ->creationRules(['required', "unique:{$connection}.{$table}"])
                ->updateRules(['required', "unique:{$connection}.{$table},slug,$id"]);
            $form->text('name', '名称');
            $form->hidden('value');

            $form->radio('type', '变量类型')->options(function () {
                $elements = Model::$elements;
                $support = [];
                foreach ($elements as $el) {
                    $support[$el] = Support::trans('configx.element.' . $el);
                }
                return $support;
            })->default('normal')->loadConfigxTypeInfo();
            $form->hidden('sort', '排序');

            $default_value_label = '默认设置值';
            if ($form->isEditing()) {
                $option = ConfigxTool::formatOption($form->model()->option);
                $optArr = [];
                if ($form->model()->option) {
                    $optArr = isset($option['options']) ? $option['options'] : [];
                }
                $element = '';
                if (in_array($form->model()->type, ['switch', 'time', 'date', 'datetime', 'icon', 'color', 'radio', 'select', 'textarea', 'rate', 'number'])) {
                    $element = $form->model()->type;
                }
                if ($form->model()->type == 'normal') {
                    $element = isset($option['element']) ? $option['element'] : 'text';
                }
                if ($element) {
                    $field = null;
                    if (Form::findFieldClass($element)) {
                        $field = call_user_func_array(
                            [$form, $element],
                            ['default_value', $default_value_label]
                        );
                        // $form->&$element('default_value', $default_value_label);
                    } else {
                        $field = $form->text('default_value', $default_value_label);
                    }
                    switch ($element) {
                        case 'radio':
                        case 'select':
                            $field->options(ConfigxTool::selectOption($optArr));
                            break;
                    };
                }
            } else {
                $form->hidden('default_value', $default_value_label);
            }
            $form->textarea('description', '帮助说明');
            $form->textarea('option', '扩展项')->help(view('jjkkopq.dcat-configx::tips'));
        })->creating(function (Form $form) {
            switch ($form->type) {
                case 'date':
                    $form->default_value = today()->format('Y-m-d');
                    break;
                case 'datetime':
                    $form->default_value = today()->format('Y-m-d H:i:s');
                    break;
                case 'icon':
                    $form->default_value = 'fa-code';
                    break;
                case 'color':
                    $form->default_value = '#ccc';
                    break;
                case 'multipleImage':
                    // case 'checkbox':
                    // case 'multipleSelect':
                    // case 'listbox':
                    $form->default_value = json_encode([]);
                    break;
                case 'radio':
                case 'select':
                    $optArr = [];
                    if ($form->model()->option) {
                        $optArr = ConfigxTool::formatOption($form->model()->option);
                    }
                    $form->default_value = $optArr[0] ?? '';
                    break;
            }
        })->saving(function (Form $form) {
            // if (Str::contains($form->name, ['.'])) {
            //     // 中断后续逻辑
            //     return $form->response()->error('系统配置名，无法设置');
            // }
        });
    }
}
