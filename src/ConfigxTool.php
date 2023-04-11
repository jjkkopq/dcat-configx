<?php

namespace Jjkkopq\DcatConfigx;

use Dcat\Admin\Form;
use \Dcat\Admin\Widgets\Form as WidgetForm;
use Dcat\Admin\Admin;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Dcat\Admin\Form\NestedForm;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Dcat\Admin\Form\Field\MultipleFile;
use Jjkkopq\DcatConfigx\Models\ConfigxModel;

class ConfigxTool
{
    /**
     * 转换格式
     */
    public static function formatValue($v)
    {
        switch ($v['type']) {
            case 'switch':
                $v['value'] = $v['value'] ? 1 : 0;
                $v['default_value'] = $v['default_value'] ? 1 : 0;
                break;
            case 'checkbox':
            case 'table':
            case 'listbox':
            case 'multipleSelect':
            case 'map':
                $v['value'] = is_string($v['value']) && is_json($v['value']) ? json_decode($v['value'], true) : $v['value'];
                $v['default_value'] = is_string($v['default_value']) && is_json($v['default_value']) ? json_decode($v['default_value'], true) : $v['default_value'];
                break;
            case 'multipleImage':
            case 'multipleFile':
                if (is_string($v['value'])) {
                    $v['value'] = explode(',', $v['value']);
                }
                break;
        }
        return $v;
    }
    /**
     * 转换格式
     */
    public static function defaultValue($v)
    {
        switch ($v['type']) {
            case 'switch':
                if ($v['value'] == 0 || $v['value'] == 1) {
                    return $v['value'];
                }
                return $v['default_value'];
                break;
            case 'checkbox':
            case 'table':
            case 'listbox':
            case 'multipleSelect':
                if (is_array($v['value']) && count($v['value']) > 0) {
                    return $v['value'];
                }
                return $v['default_value'];
                break;
            case 'map':
                if (is_array($v['value']) && count($v['value']) > 0) {
                    return ['lat' => $v['value']['lat'], 'lng' => $v['value']['lng']];
                }
                if (is_array($v['default_value']) && count($v['default_value']) > 0) {
                    return ['lat' => $v['default_value']['lat'], 'lng' => $v['default_value']['lng']];
                }
                return null;
                break;
        }
        return $v['value'] ? $v['value'] : $v['default_value'];
    }
    /**
     * 转换option格式
     */
    public static function formatOption($optionVal, $key = null)
    {
        $optArr = ['options' => []];
        $options = explode("\r\n", $optionVal);
        foreach ($options as $v) {
            $v = str_replace([' : ', ' :', ': '], ':', $v);
            if (str_contains($v, '#')) {
                $_v = str_replace('#', '', $v);
                $opt             = explode(":", $_v);
                $optArr[$opt[0]] = $opt[1] ?? 1;
            } else {
                if (str_contains($v, ':')) {
                    $opt             = explode(":", $v);
                    $optArr['options'][$opt[0]] = $opt[1];
                } else {
                    $optArr['options'][] = $v;
                }
            }
        }
        if (is_null($key)) {
            return $optArr;
        }

        return $optArr[$key] ?? '';
    }
    public static function selectOption($optArr)
    {
        if (is_array($optArr) && count($optArr) > 0) {
            $newOptArr = [
                '' => 'Root'
            ];
            foreach ($optArr as $k => $val) {
                $newOptArr[$k] = $val;
            }
            $optArr = $newOptArr;
        }
        return $optArr;
    }

    public static function fieldInit(WidgetForm $form, $v, $slug = '')
    {
        switch ($v['type']) {
            case 'image':
            case 'multipleImage':
            case 'file':
            case 'multipleFile':
                $fieldName = $v['slug'];
                break;
            default:
                $fieldName = $slug ? Str::after($v['slug'], $slug . '.') : $v['slug'];
                break;
        }
        $fieldLabel = $v['name'];
        $option = static::formatOption($v['option']);
        $optArr = [];
        if ($v->option) {
            $optArr = isset($option['options']) ? $option['options'] : [];
        }
        $field = null;
        $element = $v['type'];
        if ($v['type'] == 'normal') {
            $element = isset($option['element']) ? $option['element'] : 'text';
        }
        if ($v['type'] == 'editer') {
            $element = isset($option['element']) ? $option['element'] : 'editer';
        }
        //双字段和单字段
        switch ($v['type']) {
            case 'map':
                $field = $form->map($fieldName . '.lat', $fieldName . '.lng', $fieldLabel);
                break;
            case 'table':
                $field = $form->table($fieldName, function (NestedForm $table) use ($optArr) {
                    foreach ($optArr as $key => $label) {
                        $table->text($key, $label);
                    }
                });
                break;
            default:
                if ($element && \Dcat\Admin\Widgets\Form::findFieldClass($element)) {
                    try {
                        $field = call_user_func_array(
                            [$form, $element],
                            [$fieldName, $fieldLabel]
                        );
                    } catch (\Exception $e) {
                        admin_warning('Error', "'" . $fieldLabel . "' call method : " . $fieldName . '$form->' . $element . "('" . implode("','", [$fieldName, $fieldLabel]) . "')" . ' failed !<br />' . $e->getMessage());
                        Log::error($e->__toString());
                    }
                } else {
                    $field = $form->text($fieldName, $fieldLabel);
                }
                break;
        };
        if (is_null($field)) return $field;
        // 选项
        switch ($v['type']) {
            case 'radio':
            case 'checkbox':
            case 'select':
            case 'multipleSelect':
            case 'listbox':
                $field->options($optArr);
                break;
            case 'image':
            case 'multipleImage':
            case 'file':
            case 'multipleFile':
                $field = $field->autoUpload()->uniqueName();
                // dd($field);
                break;
            default:
                break;
        };
        if ($v['description']) {
            $field->help($v['description']);
        }
        $field = static::callUserfunctions($field, $option);

        return $field;
    }
    public static function callUserfunctions($field, $option)
    {
        foreach ($option as $k => $v) {
            if (in_array($k, ['options', 'element'])) continue;
            if (preg_match('/^\w+/', $k)) {
                $args = array_filter(explode(',', $v));
                $args = collect($args)->map(function ($s) {
                    $s = trim($s);
                    if (preg_match('/^\d+$/', $s)) {
                        return intval($s);
                    }
                    if (preg_match('/^\d+\.\d+$/', $s)) {
                        return doubleval($s);
                    }
                    if (preg_match('/^(false|true)$/i', $s)) {
                        return strtolower($s) == 'true';
                    }
                    return preg_replace("/^\s*['\"]|['\"]\s*$/", '', $s);
                })->all();

                $method = $k;
                if ($method == 'options_url') {
                    $method = 'options';
                }
                try {
                    call_user_func_array(
                        [$field, $method],
                        $args
                    );

                    // if ($field instanceof MultipleFile && strtolower($method) == 'removable') {

                    //     $id = preg_replace('/^c_(\d+)_/i', '$1', $field->column());

                    //     $field->options(['deleteUrl' => admin_base_path('configx/delfile/' . $id)]);
                    // }
                } catch (\Exception $e) {
                    admin_warning('Error', "'" . $field->label() . "' call method : " . class_basename($field) . '->' . $method . "('" . implode("','", $args) . "')" . ' failed !<br />' . $e->getMessage());
                    Log::error($e->__toString());
                }
            }
        }

        return $field;
    }
    static function gridValueCloumn($row, $value, $column)
    {
        $optArr = [];
        if ($row->option) {
            $optArr = static::formatOption($row->option, 'options');
        }
        switch ($row->type) {
            case 'normal':
                return $column->editable();
                break;
            case 'switch':
                return $column->switch();
                break;
            case 'select':
                if (count($optArr) == 0) return;
                return $column->select(static::selectOption($optArr));
                break;
            case 'radio':
                if (count($optArr) == 0) return;
                return $column->select(static::selectOption($optArr));
                break;
            case 'checkbox':
                if (count($optArr) == 0) return;
                return $value;
                // return $column->checkbox(static::selectOption($optArr));
                break;
            case 'textarea':
                return $column->textarea();
                break;
            default:
                return $value;
                break;
        }
    }

    public static function handleInput($slug, $input, $items, $middle = '', $step = 0)
    {
        $_input = [];
        if (is_array($input)) {
            foreach ($input as $k => $val) {
                $_middle = ($middle ? $middle . '.' : '') . $k;
                $item = $items->first(function ($v) use ($slug, $_middle) {
                    switch ($v['type']) {
                        case 'image':
                        case 'multipleImage':
                        case 'file':
                        case 'multipleFile':
                            return $v->slug == $_middle;
                            break;
                        default:
                            return $v->slug == ($slug ? $slug . '.' : '') . $_middle;
                            break;
                    }
                    // return Str::contains($v->slug, $fieldName);
                });
                if ($item) {
                    switch ($item->type) {
                        case 'map':
                            $val = json_encode([
                                'lat' => $val['lat'],
                                'lng' => $val['lng']
                            ]);
                            break;
                        case 'tags':
                        case 'checkbox':
                        case 'table':
                        case 'listbox':
                        case 'multipleSelect':
                            if (is_array($val)) {
                                $val = json_encode($val);
                            }
                            break;
                        case 'multipleImage':
                        case 'multipleFile':
                            if (is_array($val)) {
                                $val = implode(',', $val);
                            }
                            break;
                    }
                }
                $_step = $step;
                if ($step == 0 && $slug && !Str::contains($slug, $k)) {
                    $_input[$slug][$k] = $item ? $val : static::handleInput($slug, $val, $items, $_middle, ++$_step);
                } else {
                    $_input[$k] = $item ? $val : static::handleInput($slug, $val, $items, $_middle, ++$_step);
                }
            }
        }
        return $_input;
    }
}
