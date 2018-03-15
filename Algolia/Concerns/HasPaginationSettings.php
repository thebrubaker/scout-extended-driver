<?php

namespace App\Search\Algolia\Concerns;

trait HasPaginationSettings
{
    /**
     * Maximum number of hits per page.
     *
     * @var integer
     */
    protected $hitsPerPage = 20;

    /**
     * Maximum number of hits accessible via pagination.
     *
     * By default, this parameter is set to 1000 to guarantee good performance.
     *
     * @var integer
     */
    protected $paginationLimitedTo = 1000;

    /**
     * Return the highlight and snippet settings.
     * @return array
     */
    public function getPaginationSettings()
    {
        return [
            'hitsPerPage'         => $this->hitsPerPage,
            'paginationLimitedTo' => $this->paginationLimitedTo,
        ];
    }
}
