<?php

namespace App\Search\Algolia\Concerns;

trait HasAttributeSettings
{
    /**
     * List of attributes eligible for textual search.
     *
     * In search engine parlance, those attributes will be “indexed”, i.e.
     * their content will be made searchable.
     *
     * If not specified or empty, all string values of all attributes are
     * indexed. If specified, only the specified attributes are indexed;
     * any numerical values within those attributes are converted to strings
     * and indexed.
     *
     * When an attribute is listed, it is recursively processed, i.e. all
     * of its nested attributes, at any depth, are indexed according to the
     * same policy.
     *
     * @var array
     */
    protected $searchableAttributes = [];

    /**
     * List of attributes to use for faceting.
     *
     * All strings within these attributes will be extracted and added as
     * facets. If not specified or empty, no attribute will be faceted.
     *
     * If you want to search for values of a given facet (using the Search for
     * facet values method) you need to specify searchable(attributeName).
     *
     * If you only need to filter on a given facet, you can specify
     * filterOnly(attributeName). It reduces the size of the index and the
     * build time.
     *
     * Note that it is invalid to specify both searchable() and filterOnly()
     * at the same time (i.e. filterOnly(searchable(attributeName))).
     *
     * @var array
     */
    protected $attributesForFaceting = [];

    /**
     * List of attributes that cannot be retrieved at query time.
     *
     * These attributes can still be used for indexing and/or ranking.
     *
     * @var array
     */
    protected $unretrievableAttributes = [];

    /**
     * List of object attributes you want to retrieve.
     *
     * This can be used to minimize the size of the response.
     *
     * You can use * to retrieve all values.
     *
     * @var array
     */
    protected $attributesToRetrieve;

    /**
     * Return the attribute settings for this index.
     * @return array
     */
    public function getAttributeSettings()
    {
        return [
            'searchableAttributes'    => $this->searchableAttributes,
            'attributesForFaceting'   => $this->attributesForFaceting,
            'unretrievableAttributes' => $this->unretrievableAttributes,
            'attributesToRetrieve'    => $this->attributesToRetrieve,
        ];
    }
}
