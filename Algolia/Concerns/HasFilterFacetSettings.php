<?php

namespace App\Search\Algolia\Concerns;

trait HasFilterFacetSettings
{
    /**
     * The number of facet values retrieved when searching.
     *
     * For performance reasons, the API enforces a hard limit of 1000 on
     * maxValuesPerFacet. Any value above that limit will be interpreted
     * as 1000.
     *
     * @var integer
     */
    protected $maxValuesPerFacet = 100;

    /**
     * Return the ranking settings.
     * @return array
     */
    public function getFilterFacetSettings()
    {
        return [
            'maxValuesPerFacet' => $this->maxValuesPerFacet,
        ];
    }
}
