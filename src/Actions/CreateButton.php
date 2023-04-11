<?php

namespace Jjkkopq\DcatConfigx\Actions;

use Dcat\Admin\Actions\Action;

class CreateButton extends Action
{
    protected $attr = [
        'icon' => 'fa-save',
        'class' => 'btn-success',
        'size' => 'sm',
        'title' => '',
        'name' => '',
        'url' => '',
        'target' => 'self',
    ];
    protected $arguments;

    /**
     * Create a new CreateButton instance.
     */
    public function __construct($attrs = [], $arguments = [])
    {
        $this->attr['name'] = trans('admin.new');
        $this->arguments = $arguments;

        foreach ($attrs as $k => $val) {
            $this->attr[$k] = $val;
        }
    }

    public function getUrl()
    {
        $queryString = '';

        if ($this->arguments) {
            $queryString = http_build_query($this->arguments);
        }

        return $this->attr['url'] . ($queryString ? ('?' . $queryString) : '');
    }

    /**
     * @return string
     */
    protected function html()
    {
        $this->appendHtmlAttribute('class', "btn btn-{$this->attr['size']} {$this->attr['class']}");

        return <<<HTML
<a {$this->formatHtmlAttributes()} href='{$this->getUrl()}' target='{$this->attr['target']}'>
    <i class="fa {$this->attr['icon']}"></i>
    <span class="d-none d-sm-inline">&nbsp;{$this->attr['name']}</span>
</a>
HTML;
    }
    /**
     * Render CreateButton.
     *
     * @return string
     */
//     public function render()
//     {
//         return <<<EOT

// <div class="btn-group" style="margin-right: 5px;margin-right: 5px">
//     <a href="{$this->getUrl()}" class="btn btn-{$this->attr['size']} {$this->attr['class']}" title="{$this->attr['title']}" target="{$this->attr['target']}">
//         <i class="fa {$this->attr['icon']}"></i><span class="hidden-xs">&nbsp;&nbsp;{$this->attr['name']}</span>
//     </a>
// </div>

// EOT;
//     }
}
