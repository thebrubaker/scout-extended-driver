<?php

namespace App\Search\Algolia\Concerns;

trait HasRankingSettings
{
    /**
     * Controls the way results are sorted.
     *
     * You must specify a list of ranking criteria. They will be applied in
     * sequence by the tie-breaking algorithm in the order they are specified.
     *
     * @var array
     */
    protected $ranking = [
        'typo',
        'geo',
        'words',
        'filters',
        'proximity',
        'attribute',
        'exact',
        'custom',
    ];

    /**
     * Specifies the custom ranking criterion.
     *
     * Each string must conform to the syntax asc(${attributeName}) or
     * desc(${attributeName}) and specifies a (respectively) increasing
     * or decreasing sort on an attribute. All sorts are applied in
     * sequence by the tie-breaking algorithm in the order they are
     * specified.
     *
     * @var array
     */
    protected $customRanking = [];

    /**
     * List of indices to which you want to replicate all write operations.
     *
     * In order to get relevant results in milliseconds, we pre-compute part
     * of the ranking during indexing. Consequently, if you want to use
     * different ranking formulas depending on the use case, you need to
     * create one index per ranking formula.
     *
     * Multiple ranking formulas are useful to implement sort strategies
     * (e.g. sort by price ASC, price DESC, …).
     *
     * This option allows you to perform write operations on a single, primary
     * index and automatically perform the same operations on all of its
     * replicas.
     *
     * @var array
     */
    protected $replicas = [];

    /**
     * Return the ranking settings.
     * @return array
     */
    public function getRankingSettings()
    {
        return [
            'ranking'       => $this->ranking,
            'customRanking' => $this->customRanking,
            'replicas'      => $this->replicas,
        ];
    }
}
