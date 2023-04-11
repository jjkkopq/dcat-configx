<?php

namespace Jjkkopq\DcatConfigx\Http\Controllers;

use Dcat\Admin\Form;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Widgets\Box;
use Dcat\Admin\Widgets\Tab;
use Dcat\Admin\Widgets\Modal;
use Dcat\Admin\Layout\Content;
use Jjkkopq\DcatConfigx\Support;
use Illuminate\Routing\Controller;
use Jjkkopq\DcatConfigx\Actions\CreateButton;
use Jjkkopq\DcatConfigx\Widgets\Forms\SiteForm;
use Jjkkopq\DcatConfigx\Models\ConfigxTabsModel;
use Jjkkopq\DcatConfigx\Widgets\Forms\AdminForm;
use Jjkkopq\DcatConfigx\Widgets\Forms\FieldForm;

class AdminController extends Controller
{
    public function index(Content $content): Content
    {
        // dd(config());
        // dd(\App\Models\Modules\Config::all());
        // $item = \App\Models\Modules\Config::find(61);
        // dd(json_decode($item->description, true));

        return $content->header('增强配置')
            ->description('提供了一些对站点增强的配置')
            ->body(function (Row $row) {
                $box = new Box(' ');
                $box->tool(new CreateButton([
                    'class' => 'btn-default',
                    'icon' => 'fa-list',
                    'url' => admin_route('dcat-configx.tabs.index'),
                    'name' => Support::trans('configx.tabs'),
                    // 'target' => '_blank'
                ]));
                $box->tool(new CreateButton([
                    'class' => 'btn-default',
                    'icon' => 'fa-list',
                    'url' => admin_route('dcat-configx.index'),
                    'name' => Support::trans('configx.config'),
                    // 'target' => '_blank'
                ]));
                $box->tool(new CreateButton([
                    'class' => 'btn-primary',
                    'icon' => 'fa-plus',
                    'url' => admin_route('dcat-configx.create'),
                    'name' => trans('admin.create'),
                    // 'target' => '_blank'
                ]));
                $tab = new Tab();
                $tab->add('站点配置', new AdminForm(), true);
                // $tab->add('前站配置', new SiteForm());
                $tabs = (new ConfigxTabsModel)->allNodes();
                foreach ($tabs as $key => $v) {
                    // $tab->add($v['name'], (new FieldForm([], null, $v['slug'])), $key == 0 ? true : false);
                    $tab->add($v['name'], (new FieldForm([], null, $v['slug'])));
                }
                // $tab->dropdown([
                //     [
                //         '&nbsp;&nbsp;<i class="fa fa-list"></i>&nbsp;' . Support::trans('configx.tabs') ,
                //         admin_route('dcat-configx.tabs.index')
                //     ],
                // ]);
                $box->content($tab->withCard());
                $row->column(12, $box);
            });
    }
}
