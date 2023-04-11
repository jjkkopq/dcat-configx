<?php

namespace Jjkkopq\DcatConfigx\Models\Traits;

use Illuminate\Support\Facades\Cache;

trait HasCache
{
    protected $cacheKey = 'dcat-admin-jjkkopq-%s';

    /**
     * Get an item from the cache, or execute the given Closure and store the result.
     *
     * @param  \Closure  $builder
     * @return mixed
     */
    protected function remember(\Closure $builder)
    {
        return $this->getStore()->remember($this->getCacheKey(), null, $builder);
    }

    /**
     * @return bool|void
     */
    public function flushCache()
    {
        return $this->getStore()->delete($this->getCacheKey());
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
     * @return string
     */
    protected function getCacheKey()
    {
        return sprintf($this->cacheKey, $this->getTable());
    }

    /**
     * Get cache store.
     *
     * @return \Illuminate\Contracts\Cache\Repository
     */
    public function getStore()
    {
        return Cache::store(config('cache.default'));
    }
}
