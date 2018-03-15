<?php

namespace App\Search\Algolia\Concerns;

trait HasTypoSettings
{
    /**
     * Minimum number of characters a word in the query string must contain
     * to accept matches with one typo.
     *
     * @var integer
     */
    protected $minWordSizefor1Typo = 4;

    /**
     * Minimum number of characters a word in the query string must contain
     * to accept matches with two typos.
     *
     * @var integer
     */
    protected $minWordSizefor2Typos = 8;

    /**
     * Controls whether typo tolerance is enabled and how it is applied:
     *
     * true: Typo tolerance is enabled and all matching hits are retrieved
     * (default behavior).
     *
     * false: Typo tolerance is entirely disabled. Hits matching with only
     * typos are not retrieved.
     *
     * min: Only keep results with the minimum number of typos. For example,
     * if just one hit matches without typos, then all hits with only typos
     * are not retrieved.
     *
     * strict: Hits matching with 2 typos or more are not retrieved if there
     * are some hits matching without typos. This option is useful to avoid
     * “false positives” as much as possible.
     *
     * @var boolean|string
     */
    protected $typoTolerance = true;

    /**
     * Whether to allow typos on numbers (“numeric tokens”) in the query
     * string.
     *
     * When false, typo tolerance is disabled on numeric tokens. For example,
     * the query 304 will match 30450 but not 40450 (which would have been
     * the case with typo tolerance enabled).
     *
     * @var boolean
     */
    protected $allowTyposOnNumericTokens = true;

    /**
     * Consider singular and plurals forms a match without typo.
     * For example, “car” and “cars”, or “foot” and “feet” will be considered
     * equivalent.
     *
     * This parameter may be:
     * a boolean: enable or disable plurals for all supported languages;
     * a list of language ISO codes for which plurals should be enabled.
     *
     * @var boolean|array
     */
    protected $ignorePlurals = false;

    /**
     * List of attributes on which you want to disable typo tolerance
     *
     * The list must be a subset of the searchableAttributes index setting.
     *
     * @var array
     */
    protected $disableTypoToleranceOnAttributes = [];

    /**
     * List of words on which typo tolerance will be disabled.
     * @var array
     */
    protected $disableTypoToleranceOnWords = [];

    /**
     * Separators (punctuation characters) to index.
     *
     * By default, separators are not indexed.
     *
     * Example: Use +# to be able to search for “Google+” or “C#”.
     *
     * @var string
     */
    protected $separatorsToIndex = '';

    /**
     * Return the typo settings.
     * @return array
     */
    public function getTypoSettings()
    {
        return [
            'minWordSizefor1Typo'              => $this->minWordSizefor1Typo,
            'minWordSizefor2Typos'             => $this->minWordSizefor2Typos,
            'typoTolerance'                    => $this->typoTolerance,
            'allowTyposOnNumericTokens'        => $this->allowTyposOnNumericTokens,
            'ignorePlurals'                    => $this->ignorePlurals,
            'disableTypoToleranceOnAttributes' => $this->disableTypoToleranceOnAttributes,
            'disableTypoToleranceOnWords'      => $this->disableTypoToleranceOnWords,
            'separatorsToIndex'                => $this->separatorsToIndex,
        ];
    }
}
