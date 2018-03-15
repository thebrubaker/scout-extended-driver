<?php

namespace App\Search\Algolia\Concerns;

trait HasPerformanceSettings
{
    /**
     * List of numeric attributes that can be used as numerical filters.
     *
     * If not specified, all numeric attributes are automatically indexed and
     * available as numerical filters (via the filters parameter). If
     * specified, only attributes explicitly listed are available as numerical
     * filters. If empty, no numerical filters are allowed.
     *
     * If you don’t need filtering on some of your numerical attributes, you
     * can use numericAttributesForFiltering to speed up the indexing.
     *
     * If you only need to filter on a numeric value based on equality (i.e.
     * with the operators = or !=), you can speed up the indexing by specifying
     * equalOnly(${attributeName}). Other operators will be disabled.
     *
     * @var array
     */
    protected $numericAttributesForFiltering;

    /**
     * Enables compression of large integer arrays.
     *
     * In data-intensive use-cases, we recommended enabling this feature to
     * reach a better compression ratio on arrays exclusively containing
     * non-negative integers (as is typical of lists of user IDs or ACLs).
     *
     * @var boolean
     */
    protected $allowCompressionOfIntegerArray = false;

    /**
     * Return the performance settings.
     * @return array
     */
    public function getPerformanceSettings()
    {
        return [
            'numericAttributesForFiltering'  => $this->numericAttributesForFiltering,
            'allowCompressionOfIntegerArray' => $this->allowCompressionOfIntegerArray,
        ];
    }
}
