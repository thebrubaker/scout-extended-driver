<?php

namespace App\Search;

use App\Search\Algolia\Index;
use App\User;

class UserIndex extends Index
{
	/**
	 * List of attributes to use for faceting.
	 * @var array
	 */
	protected $attributesForFaceting = [];

    /**
     * The name of the index.
     * @var string
     */
    protected $name = 'users';

    /**
     * Relationships to eager load.
     * @var array
     */
    protected $with = [];

    /**
     * The class of the model to index.
     * @return string
     */
    public function class()
    {
        return User::class;
    }

    /**
     * Scope the query for retrieving models.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeQuery($query)
    {
        // $query->where();
    }

    /**
     * Return an array of searchable attributes for
     * the index.
     *
     * @param  User $user
     *
     * @return array
     */
    public function toSearchableArray(User $user)
    {
        return [
        	'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
        ];
    }
}
