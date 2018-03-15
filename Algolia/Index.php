<?php

namespace App\Search\Algolia;

use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

abstract class Index
{
    use Concerns\HasAttributeSettings,
        Concerns\HasRankingSettings,
        Concerns\HasFilterFacetSettings,
        Concerns\HasHighlightSnippetSettings,
        Concerns\HasPaginationSettings,
        Concerns\HasTypoSettings,
        Concerns\HasQuerySettings,
        Concerns\HasPerformanceSettings;

    /**
     * The name of the index.
     * @var string
     */
    protected $name = '';

    /**
     * The attributes that should not be written to the index.
     * @var array
     */
    protected $hidden = [];
    
    /**
     * The relationships to eager load.
     * @var array
     */
    protected $with = [];

    /**
     * The class of the model to index.
     * @return string
     */
    abstract public function class();

    /**
     * Get the name of the index. Default name is the table name of the model.
     *
     * @param string $prefix
     *
     * @return string
     */
    public function getName($prefix = '')
    {
        $base = $this->name ?: $this->getModelName();

        return empty($prefix) ? $base : $prefix . '_' . $base;
    }

    /**
     * Get an instance of the model.
     * @return Model
     */
    public function getModel()
    {
        return app($this->class());
    }

    /**
     * Get the model name.
     * @return string
     */
    public function getModelName()
    {
        return $this->getModel()->getTable();
    }

    /**
     * Chunk a number of models through a callback. Scoping, checks, and
     * and transformations are applied to each model before being
     * passed to the callback,
     *
     * @param  int      $number
     * @param  Callable $callback
     *
     * @return void
     */
    public function batchPrepare($number, $callback)
    {
        $query = $this->getModel()->query();

        if (method_exists($this, 'scopeQuery')) {
            $this->scopeQuery($query);
        }

        $query->chunk($number, function ($models) use ($callback) {
            call_user_func($callback, $this->prepareModels($models));
        });
    }

    /**
     * Filter and transform models.
     *
     * @param  Collection $models
     *
     * @return void
     */
    public function prepareModels($models)
    {
        $models->load($this->with);
        
        $filtered = $models->filter(function ($model) {
            if (method_exists($this, 'shouldIndex')) {
                return $this->shouldIndex($model);
            }

            return true;
        });

        return $filtered->map(function ($model) {
            return $this->transform($model);
        });
    }

    /**
     * Filter and transform models to be destroyed.
     *
     * @param  Collection $models
     *
     * @return void
     */
    public function prepareDeleteModels($models)
    {
        $filtered = $models->filter(function ($model) {
            return $this->shouldIndex($model);
        });

        $keys = $filtered->map(function ($model) {
            return $model->getKey();
        });

        return $keys->values()->all();
    }

    /**
     * Transform a model into a object data for an Algolia index.
     *
     * @param  Model $model
     *
     * @return array
     */
    public function transform(Model $model)
    {
        if (method_exists($this, 'toSearchableArray')) {
            $data = $this->toSearchableArray($model);
        } else {
            $data = $model->toArray();
        }

        $data = $this->stripHiddenAttributes($data, $this->getHiddenAttributes());

        $data['objectID'] = $model->getKey();

        return $data;
    }

    /**
     * Strip defined hidden attributes from an array of data.
     *
     * @param  array $data
     * @param  array $hidden
     *
     * @return array
     */
    public function stripHiddenAttributes($data, $hidden)
    {
        foreach ($hidden as $key => $value) {
            if (is_array($value) && array_key_exists($key, $data)) {
                $data[$key] = $this->stripHiddenAttributes(
                    $data[$key],
                    $value
                );
            }

            if (!is_array($value) && array_key_exists($value, $data)) {
                unset($data[$value]);
            }
        }

        return $data;
    }

    /**
     * Returns array of keys that should not be indexed.
     * @return array
     */
    public function getHiddenAttributes()
    {
        return $this->hidden;
    }

    /**
     * Return an array of the index settings.
     * @return array
     */
    public function getSettings()
    {
        return array_merge(
            $this->getAttributeSettings(),
            $this->getRankingSettings(),
            $this->getFilterFacetSettings(),
            $this->getHighlightSnippetSettings(),
            $this->getPaginationSettings(),
            $this->getTypoSettings(),
            $this->getQuerySettings(),
            $this->getPerformanceSettings()
        );
    }
}