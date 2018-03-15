<?php

namespace Tests\Unit\Search;

use App\Search\Algolia\Index;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class MockUserIndex extends Index
{
    /**
     * The name of the index.
     * @var string
     */
    protected $name = 'users';

    /**
     * The attributes that should not be written to the index.
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * The class of the model for the index.
     * @return string
     */
    public function class()
    {
        return User::class;
    }

    /**
     * Prepend the index name.
     * @return string
     */
    public function prependIndex()
    {
        return 'prefix';
    }

    /**
     * Determine if the model should be indexed.
     *
     * @param  User $user
     *
     * @return bool
     */
    public function shouldIndex(User $user)
    {
        return $user->exists();
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
        return $query;
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
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];
    }
}