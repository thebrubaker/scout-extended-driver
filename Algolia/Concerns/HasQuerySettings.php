<?php

namespace App\Search\Algolia\Concerns;

trait HasQuerySettings
{
    /**
     * Controls if and how query words are interpreted as prefixes.
     *
     * It may be one of the following values:
     *
     * prefixLast: Only the last word is interpreted as a prefix
     * (default behavior).
     *
     * prefixAll: All query words are interpreted as prefixes. This option
     * is not recommended, as it tends to yield counterintuitive results
     * and has a negative impact on performance.
     *
     * prefixNone: No query word is interpreted as a prefix. This option
     * is not recommended, especially in an instant search setup, as the user
     * will have to type the entire word(s) before getting any relevant
     * results.
     *
     * @var string
     */
    protected $queryType = 'prefixLast';

    /**
     * Selects a strategy to remove words from the query when it doesn’t match
     * any hits.
     *
     * The goal is to avoid empty results by progressively loosening the query
     * until hits are matched.
     *
     * There are four different options:
     *
     * none: No specific processing is done when a query does not return any
     * results (default behavior).
     *
     * lastWords: When a query does not return any results, treat the last
     * word as optional. The process is repeated with words N-1, N-2, etc.
     * until there are results, or the beginning of the query string has been
     * reached.
     *
     * firstWords: When a query does not return any results, treat the first
     * word as optional. The process is repeated with words 2, 3, etc. until
     * there are results, or the end of the query string has been reached.
     *
     * allOptional: When a query does not return any results, make a second
     * attempt treating all words as optional. This is equivalent to
     * transforming the implicit AND operator applied between query words
     * to an OR.
     *
     * @var string
     */
    protected $removeWordsIfNoResults = 'none';

    /**
     * List of words that should be considered as optional when found in the
     * query.
     *
     * @var array
     */
    protected $optionalWords = [];

    /**
     * Enables the advanced query syntax.
     *
     * @var boolean
     */
    protected $advancedSyntax = false;

    /**
     * Remove stop words from the query before executing it. Stop word removal
     * is useful when you have a query in natural language, e.g. “what is a
     * record?”. In that case, the engine will remove “what”, “is” and “a”
     * before executing the query, and therefore just search for “record”.
     *
     * This parameter may be a boolean or a list of language ISO codes for
     * which stop word removal should be enabled.
     *
     * @var boolean|array
     */
    protected $removeStopWords = false;

    /**
     * List of attributes on which you want to disable prefix matching.
     *
     * This setting is useful on attributes that contain string that should
     * not be matched as a prefix (for example a product SKU).
     *
     * @var array
     */
    protected $disablePrefixOnAttributes = [];

    /**
     * List of attributes on which you want to disable computation of the exact
     * ranking criterion.
     *
     * @var array
     */
    protected $disableExactOnAttributes = [];

    /**
     * Controls how the exact ranking criterion is computed when the query
     * contains only one word.
     *
     * The following values are allowed:
     *
     * attribute (default): the exact ranking criterion is set to 1 if the
     * query string exactly matches an entire attribute value. For example,
     * if you search for the TV show “V”, you want it to match the query “V”
     * before all popular TV shows starting with the letter V.
     *
     * none: the exact ranking criterion is ignored on single word queries;
     *
     * word: the exact ranking criterion is set to 1 if the query word is
     * found in the record. The query word must be at least 3 characters
     * long and must not be a stop word in any supported language.
     *
     * @var string
     */
    protected $exactOnSingleWordQuery = 'attribute';

    /**
     * List of alternatives that should be considered an exact match by the exact ranking criterion.
     *
     * The following values are allowed:
     *
     * ignorePlurals: alternative words added by the ignorePlurals feature;
     *
     * singleWordSynonym: single-word synonyms (example: “NY” = “NYC”);
     *
     * multiWordsSynonym: multiple-words synonyms (example: “NY” = “New York”).
     *
     * @var array
     */
    protected $alternativesAsExact = [
        'ignorePlurals',
        'singleWordSynonym',
    ];

    /**
     * Return the query settings.
     * @return array
     */
    public function getQuerySettings()
    {
        return [
            'queryType'                 => $this->queryType,
            'removeWordsIfNoResults'    => $this->removeWordsIfNoResults,
            'optionalWords'             => $this->optionalWords,
            'advancedSyntax'            => $this->advancedSyntax,
            'removeStopWords'           => $this->removeStopWords,
            'disablePrefixOnAttributes' => $this->disablePrefixOnAttributes,
            'disableExactOnAttributes'  => $this->disableExactOnAttributes,
            'exactOnSingleWordQuery'    => $this->exactOnSingleWordQuery,
            'alternativesAsExact'       => $this->alternativesAsExact,
        ];
    }
}
