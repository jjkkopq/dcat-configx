<?php

namespace Jjkkopq\DcatConfigx\Models;

use Spatie\EloquentSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\SortableTrait;
use Dcat\Admin\Traits\HasDateTimeFormatter;
use Jjkkopq\DcatConfigx\Models\Traits\HasCache;

class ConfigxModel extends Model implements Sortable
{
    use SortableTrait;
    use HasDateTimeFormatter;
    use HasCache;
    protected $table = 'admin_configx';

    protected $fillable = ['slug', 'name', 'type', 'value', 'option', 'description', 'sort'];
    /**
     * @var array
     */
    protected $sortable = [
        'order_column_name' => 'sort',
        'sort_when_creating' => true,
    ];
    protected $orderColumn = 'sort';


    public static $elements = [
        'normal', 'date', 'time', 'datetime', 'image', 'multipleImage', 'password', 'file', 'multipleFile',
        'switch', 'rate', 'editor', 'tags', 'icon', 'color', 'number', 'table', 'textarea',
        'radio', 'checkbox', 'listbox', 'select', 'multipleSelect', 'map',
    ];

    public function __construct(array $attributes = [])
    {
        $this->connection = config('admin.database.connection') ?: config('database.default');

        parent::__construct($attributes);
    }

    public function setValueAttribute($value = null)
    {
        $this->attributes['value'] = is_null($value) ? '' : $value;
    }

    /**
     * Get all elements.
     *
     * @return static[]|\Illuminate\Support\Collection
     */
    public function allNodes()
    {
        return $this->remember(function () {
            return (new static())->all();
        });
    }
    /**
     * Detach models from the relationship.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            $model->flushCache();
        });
        static::saved(function ($model) {
            $model->flushCache();
        });
    }
}
