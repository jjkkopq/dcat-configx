<?php

/*
 * // +----------------------------------------------------------------------
 * // | erp
 * // +----------------------------------------------------------------------
 * // | Copyright (c) 2006~2020 erp All rights reserved.
 * // +----------------------------------------------------------------------
 * // | Licensed ( LICENSE-1.0.0 )
 * // +----------------------------------------------------------------------
 * // | Author: yxx <1365831278@qq.com>
 * // +----------------------------------------------------------------------
 */

namespace Jjkkopq\DcatConfigx\Form\Field;

use Dcat\Admin\Form;
use Dcat\Admin\Admin;

class RadioMacro
{
    public static function macro(): void
    {
        // static::loadpku();
        // static::with_order();
        static::loadConfigxTabInfo();
        static::loadConfigxTypeInfo();
    }

    protected static function loadConfigxTabInfo(): void
    {
        // 加载pku动态选择
        Form\Field\Radio::macro('loadConfigxTabInfo', function () {
            $slugClass  = static::FIELD_CLASS_PREFIX . 'slug';
            $nameClass  = static::FIELD_CLASS_PREFIX . 'name';

            $script = <<<JS
// $(document).off('change', "{$this->getElementClassSelector()}");
$(document).on('change', "{$this->getElementClassSelector()}", function () {
                    var slug = $(this).closest('.fields-group').find(".$slugClass");
                    var name = $(this).closest('.fields-group').find(".$nameClass");
                    if (slug) slug.val(this.value ? this.value + '.new_key_here' : '');
});
JS;

            Admin::script($script);

            return $this;
        });
    }
    protected static function loadConfigxTypeInfo(): void
    {
        // 加载pku动态选择
        Form\Field\Radio::macro('loadConfigxTypeInfo', function () {
            $script = <<<JS
// $(document).off('change', "{$this->getElementClassSelector()}");
$(document).on('change', "{$this->getElementClassSelector()}", function () {
                    var value = this.value;
        $('div.elem').addClass('hidden');
        if (value == 'radio' || value == 'checkbox' || value == 'select' || value == 'multipleSelect' || value == 'listbox') {
            $('.group_elem').removeClass('hidden');
            if (value == 'select' || value == 'multipleSelect') {
                $('.select_elem').removeClass('hidden');
            } else {
                $('.select_elem').addClass('hidden');
            }
        } else if (value == 'textarea') {
            $('.textarea_elem').removeClass('hidden');
        } else if (value == 'number') {
            $('.number_elem').removeClass('hidden');
        } else if (value == 'color') {
            $('.color_elem').removeClass('hidden');
        } else if (value == 'table') {
            $('.table_elem').removeClass('hidden');
        } else if (value == 'editor') {
            $('.editor_elem').removeClass('hidden');
        } else if (value == 'image' || value == 'multipleImage') {
            $('.image_elem').removeClass('hidden');
        } else if (value == 'file' || value == 'multipleFile') {
            $('.file_elem').removeClass('hidden');
        } else if (value == 'map') {
            $('.map_elem').removeClass('hidden');
        } else if (value == 'normal') {
            $('.normal_elem').removeClass('hidden');
        }
});
$("{$this->getElementClassSelector()}").trigger('change');
JS;

            Admin::script($script);

            return $this;
        });
    }
}
