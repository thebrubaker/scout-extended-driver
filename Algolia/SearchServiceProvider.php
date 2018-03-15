<?php

namespace App\Search;

use AlgoliaSearch\Client as Algolia;
use App\Search\Algolia\ExtendedEngine;
use Illuminate\Support\ServiceProvider;
use AlgoliaSearch\Version as AlgoliaUserAgent;
use Laravel\Scout\EngineManager;

class SearchServiceProvider extends ServiceProvider
{
    /**
     * The indices to build.
     * @var array
     */
    protected $indices = [
        \App\Search\UserIndex::class,
    ];

    /**
     * Bootstrap any application Services.
     *
     * @return void
     */
    public function boot()
    {
        // Extend the engine manager with a new driver called algolia_extended.
        resolve(EngineManager::class)->extend('algolia_extended', function () {
            return $this->createExtendedEngine();
        });
    }

    /**
     * Create an instance of the extended Algolia engine. This driver extends
     * the base Scout AlgoliaEngine and adds some new methods for working with
     * the new Index classes.
     * @return ExtendedEngine
     */
    public function createExtendedEngine()
    {
        AlgoliaUserAgent::$custom_value = '; Laravel Scout (algolia custom driver)';

        $engine = new ExtendedEngine(new Algolia(
            config('scout.algolia.id'), config('scout.algolia.secret')
        ));

        $engine->setIndices($this->indices);

        return $engine;
    }
}
