<?php

namespace Jjkkopq\DcatConfigx\Widgets\Forms;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Dcat\Admin\Widgets\Form;
use Jjkkopq\DcatConfigx\Configx;
use \Jjkkopq\DcatConfigx\Support;
use Dcat\Admin\Traits\LazyWidget;
use Illuminate\Support\Facades\DB;
use Jjkkopq\DcatConfigx\ConfigxTool;
use Dcat\Admin\Extend\ServiceProvider;
use Dcat\Admin\Contracts\LazyRenderable;
use Jjkkopq\DcatConfigx\Models\ConfigxModel;
use Jjkkopq\DcatConfigx\ConfigxServiceProvider;

class FieldForm extends Form
{
    use LazyWidget; // 使用异步加载功能

    protected $slug = '';
    /**
     * Form constructor.
     *
     * @param  string  $slug
     * @param  array  $data
     * @param  mixed  $key
     */
    function __construct($data = [], $key = null, string $slug = '')
    {
        $this->slug = $slug;
        $this->payload(['slug' => $this->slug]);
        if ($data) {
            $this->fill($data);
        }
        $this->setKey($key);
        $this->setUp();
    }
    /**
     * Handle the form request.
     *
     * @param array $input
     *
     * @return mixed
     */
    public function handle(array $input)
    {
        $slug = $this->payload['slug'] ?? $this->slug;
        $items = Configx::group($slug);
        // 多级数组遍历递归处理
        $input = ConfigxTool::handleInput($slug, $input, $items);
        $configs = Arr::dot($input);
        DB::beginTransaction();
        try {
            foreach ($items as $v) {
                if ($slug) {
                    if (isset($configs[$v['slug']])) {
                        ConfigxModel::where([
                            'slug' => $v['slug']
                        ])->update(['value' => $configs[$v['slug']]]);
                    }
                } else {
                    if (in_array($v['slug'], ['image', 'multipleImage', 'file', 'multipleFile']) && isset($configs[$v['slug']])) {
                        ConfigxModel::where([
                            'slug' => $v['slug']
                        ])->update(['value' => $configs[$v['slug']]]);
                    }
                }
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this
                ->response()
                ->error($th->getMessage());
        }
        (new ConfigxModel())->flushCache();

        return $this
            ->response()
            ->success('站点配置更新成功！')
            ->refresh();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $slug = $this->payload['slug'] ?? $this->slug;
        $items = Configx::group($slug);
        foreach ($items as $v) {
            ConfigxTool::fieldInit($this, $v, $slug);
        }
    }
    // 返回表单数据，如不需要可以删除此方法
    public function default()
    {
        $default = [];
        $slug = $this->payload['slug'] ?? $this->slug;
        $items = Configx::group($slug);
        foreach ($items as $v) {
            switch ($v['type']) {
                case 'image':
                case 'multipleImage':
                case 'file':
                case 'multipleFile':
                    $fieldName = $v['slug'];
                    break;
                default:
                    $fieldName = $this->slug ? Str::after($v['slug'], $this->slug . '.') : $v['slug'];
                    break;
            }
            $default[$fieldName] = ConfigxTool::defaultValue($v);
        }
        return $default;
    }
}
