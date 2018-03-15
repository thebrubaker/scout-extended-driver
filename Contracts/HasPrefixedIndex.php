<?php

namespace App\Search\Contracts;

interface HasPrefixedIndex
{
    /**
     * Returns the prefix for indexing the model.
     * @return string
     */
    public function getIndexPrefix();
}