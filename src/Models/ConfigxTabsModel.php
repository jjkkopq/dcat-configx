<?php

namespace Jjkkopq\DcatConfigx\Models;

use Illuminate\Database\Eloquent\Model;
use Dcat\Admin\Traits\HasDateTimeFormatter;
use Jjkkopq\DcatConfigx\Models\Traits\HasCache;

class ConfigxTabsModel extends Model
{
    use HasDateTimeFormatter;
    use HasCache;

    protected $table = 'admin_configx_tabs';

    protected $fillable = ['name', 'slug'];

    public function __construct(array $attributes = [])
    {
        $this->connection = config('admin.database.connection') ?: config('database.default');

        parent::__construct($attributes);
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
