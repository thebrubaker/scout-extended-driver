<?php

namespace App\Search\Algolia;

use App\Search\Algolia\Index;
use Illuminate\Support\Collection;
use Laravel\Scout\Engines\AlgoliaEngine;

class ExtendedEngine extends AlgoliaEngine
{
    /**
     * The registered indices to update.
     * @var array
     */
    protected $indices = [];

    /**
     * The amount of models to update in batch.
     * @var integer
     */
    protected $batchAmount = 200;

    /**
     * {@inheritdoc}
     */
    public function update($models)
    {
        $model = $models->first();
        // Indices are prefixed with the org slug
        $prefix = $model->getPrefix();
        // Get the class of the model to update
        $class = get_class($model);
        // Get all indices that need to be updated for this model
        $indices = $this->getIndices($class);

        // if there are no registered indices for this model, perform
        // a normal scout update.
        if ($indices->isEmpty()) {
            return parent::update($models);
        }

        // otherwise we want to update the registered indices for that model,
        // so we init an index for each configuration, prepare the models,
        // and add them to Algolia.
        $indices->each(function ($config) use ($models, $prefix) {
            $index   = $this->algolia->initIndex($config->getName($prefix));
            $objects = $config->prepareModels($models);
            $index->addObjects($objects->toArray());
        });
    }

    /**
     * {@inheritdoc}
     */
    public function delete($models)
    {
        $model = $models->first();
        // Indices are prefixed with the org slug
        $prefix = $model->getPrefix();
        // Get the class of the model to update
        $class = get_class($model);
        // Get all indices that need to be updated for this model
        $indices = $this->getIndices($class);

        // if there are no registered indices for this model, perform
        // a normal scout delete.
        if ($indices->isEmpty()) {
            return parent::delete($models);
        }

        // otherwise we want to delete the models from our registered indices,
        // so we init an index for each configuration, prepare the models,
        // and delete them from Algolia.
        $indices->each(function ($config) use ($models, $prefix) {
            $index = $this->algolia->initIndex($config->getName($prefix));
            $keys  = $config->prepareDeleteModels($models);
            $index->deleteObjects($keys);
        });
    }

    /**
     * Set the indices on the engine.
     *
     * @param array $indices
     *
     * @return ExtendedEngine
     */
    public function setIndices($indices)
    {
        // Take each index class and instatiate it.
        $this->indices = collect($indices)->map(function ($class) {
            return new $class;
        });

        // Map each index to it's corresponding class name so we can
        // track which indices need to update when a model changes.
        $this->classMap = $this->createClassMap();

        return $this;
    }

    /**
     * Create a map of model names and the indices that should be updated when
     * that model changes.
     * @return \Illuminate\Support\Collection
     */
    public function createClassMap()
    {
        // Set the key as the model's class name, and the value as a collection
        // of indices. You can use this map to look up which indices need
        // to be updated when a model is updated.
        return $this->indices->reduce(function ($classMap, $index) {
            $className = get_class($index->getModel());

            // create the key for that class, or push it onto the
            // existing key.
            if ($classMap->has($className)) {
                $classMap[$className]->push($index);
            } else {
                $classMap[$className] = collect([$index]);
            }

            return $classMap;
        }, collect());
    }

    /**
     * Return a collection of indices registered with the application. If a
     * class name is provided, we will grab indices registered for that class.
     *
     * @param string $class
     *
     * @return \Illuminate\Support\Collection
     */
    public function getIndices($class = '')
    {
        if (empty($class)) {
            return $this->indices;
        }

        if ($this->classMap->has($class)) {
            return $this->classMap[$class];
        }

        return collect();
    }

    /**
     * Return a map of classes and the indices that should be updated when
     * objects of that class are updated.
     * @return \Illuminate\Support\Collection
     */
    public function getClassMap()
    {
        return $this->classMap;
    }

    /**
     * Loop through and build each index on Algolia.
     *
     * @param string $class
     * @param string $prefix
     *
     * @return \Illuminate\Support\Collection
     */
    public function buildIndices($prefix = '', $class = '')
    {
        return $this->getIndices($class)->map(function ($index) use ($prefix) {
            return $this->buildIndex($index, $prefix);
        });
    }

    /**
     * Create a new index, add objects to that index, and set the settings
     * on that index.
     *
     * @param  \App\Search\Algolia\Index $config
     * @param string                     $prefix
     *
     * @return \App\Search\Algolia\Index
     */
    public function buildIndex(Index $config, $prefix = '')
    {
        // @var \AlgoliaSearch\Index
        $index = $this->algolia->initIndex($config->getName($prefix));

        $this->batchUpdate($config, $this->batchAmount, function ($objects) use ($index) {
            $index->addObjects($objects->toArray());
        });

        $index->setSettings($config->getSettings());


        return $config;
    }

    /**
     * Batch process the models for an index configuration through a callback
     *
     * @param  \App\Search\Algolia\Index $config
     * @param  int                       $batchAmount
     * @param  Callable                  $callback
     *
     * @return void
     */
    public function batchUpdate(Index $config, $batchAmount = 200, $callback)
    {
        $config->batchPrepare($batchAmount, function ($models) use ($callback) {
            call_user_func($callback, $models);
        });
    }

    /**
     * Destroy the provided index.
     *
     * @param string $prefix
     *
     * @return void
     */
    public function destroyIndex(Index $index, $prefix = '')
    {
        $this->algolia->deleteIndex($index->getName($prefix));
    }

    /**
     * Delete each index from Algolia.
     * @return void
     */
    public function destroyIndices($prefix = '')
    {
        $existing = collect($this->algolia->listIndexes()['items'] ?? []);

        $existing->filter(function ($index) use ($prefix) {
        	return preg_match("/^$prefix/", $index['name']);
        })->each(function ($index) {
        	$this->algolia->deleteIndex($index['name']);
        });
    }

    /**
     * Refresh api keys for the provided index prefixes.
     *
     * @param  array $indexPrefixes
     *
     * @return array
     */
    public function createSearchApiKeys($indexPrefixes)
    {
        // We are going to create an API key scoped to a prefix, which
        // is usually the organization slug. So we loop through each
        // of the provided prefixes and make a corresponding API Key.
        return collect($indexPrefixes)->mapWithKeys(function ($prefix) {
            $name = $prefix . '_*';

            // Check for an existing key with this prefix name.
            $existingKey = $this->getScopedApiKeys($name)->first();

            // If it exists, just return the existing key
            if (!empty($existingKey)) {
                return [$prefix => $existingKey['value']];
            }

            // if there isn't a key for the prefix, add a new one
            $response = $this->algolia->addApiKey([
            	'acl'         => ['search'],
                'description' => "A search only API key for the following prefixed indices: $prefix",
                'indexes'     => [$name],
            ]);

            return [$prefix => $response['key']];
        });
    }

    /**
     * Return API Keys for the given prefix.
     *
     * @param string $prefix
     *
     * @return Collection
     */
    public function getScopedApiKeys($prefix)
    {
        $name = $prefix . '_*';

        return $this->listApiKeys()->filter(function ($key) use ($name) {
            return array_key_exists('indexes', $key) && in_array($name, $key['indexes']);
        })->values();
    }

    /**
     * List all API Keys.
     * @return Collection
     */
    public function listApiKeys()
    {
        return collect($this->algolia->listApiKeys()['keys']);
    }

    /**
     * Delete an existing API Key.
     *
     * @param  string $key
     *
     * @return void
     */
    public function deleteApiKey($key)
    {
        $this->algolia->deleteApiKey($key);
    }
}
