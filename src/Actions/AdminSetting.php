<?php

namespace Jjkkopq\DcatConfigx\Actions;

use Dcat\Admin\Actions\Action;
use Dcat\Admin\Widgets\Modal;
use Jjkkopq\DcatConfigx\Widgets\Forms\UserAdminForm;

class AdminSetting extends Action
{
    /**
     * @return string
     */
	protected $title = 'UI设置';

    public function render()
    {
        $modal = Modal::make()
            ->id('admin-setting-config') // 导航栏显示弹窗，必须固定ID，随机ID会在刷新后失败
            ->title($this->title())
            ->body(UserAdminForm::make())
            ->lg()
            ->button(
                <<<HTML
<ul class="nav navbar-nav">
    <li class="nav-item"> &nbsp;<i class="feather icon-edit" style="font-size: 1.5rem"></i> {$this->title()} &nbsp;</li>
</ul>
HTML
            );

        return $modal->render();
    }
}
