<?php

namespace App\Search;

use App\Search\Algolia\ExtendedEngine;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use Laravel\Scout\EngineManager;
use Mockery;
use Tests\TestCase;

/**
 * Class ExtendedEngineTest
 *
 * @package Tests\Unit\Search
 */
class ExtendedEngineTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function testEngineIsRegisteredWithIndicesInServiceProvider()
    {
        $engine = app(EngineManager::class)->engine('algolia_extended')->setIndices([
            MockUserIndex::class,
        ]);
        $this->assertTrue($engine instanceof ExtendedEngine);
        $this->assertTrue($engine->getIndices() instanceof Collection);
        $this->assertTrue($engine->getClassMap() instanceof Collection);
        $this->assertTrue($engine->getClassMap()->has('App\User'));
        $this->assertTrue($engine->getClassMap()->get('App\User')->count() === 1);
    }

    /**
     * @test
     */
    public function testEngineWillBuildIndices()
    {
        $user = factory(User::class)->create([
            'name' => 'test',
            'email' => 'test@test.com',
        ]);

        $client = Mockery::mock('AlgoliaSearch\Client');

        $client->shouldReceive('initIndex')
            ->once()
            ->with('users')
            ->andReturn($index = Mockery::mock('StdClass'));

        $index->shouldReceive('addObjects')
            ->once();

        $index->shouldReceive('setSettings')
            ->once()
            ->with((new MockUserIndex)->getSettings());

        $engine = new ExtendedEngine($client);

        $engine->setIndices([
            MockUserIndex::class,
        ]);

        $engine->buildIndices('organization');
    }

    /**
     * @test
     */
    public function testEngineWillUpdateModels()
    {
        factory(User::class)->create([
            'name' => 'test1',
            'email' => 'test1@test1.com',
        ]);

        factory(User::class)->create([
            'name' => 'test2',
            'email' => 'test2@test2.com',
        ]);

        $users = User::all();

        $client = Mockery::mock('AlgoliaSearch\Client');

        $client->shouldReceive('initIndex')
            ->once()
            ->with($this->organization->slug . '_users')
            ->andReturn($index = Mockery::mock('StdClass'));

        $index->shouldReceive('addObjects')
            ->once();

        $engine = new ExtendedEngine($client);

        $engine->setIndices([
            MockUserIndex::class,
        ]);

        $engine->update($users);
    }

    /**
     * @test
     */
    public function testEngineWillDeleteModels()
    {
        $users = collect([
            factory(User::class)->create([
                'name' => 'test1',
                'email' => 'test1@test1.com',
            ]),
            factory(User::class)->create([
                'name' => 'test2',
                'email' => 'test2@test2.com',
            ]),
        ]);

        $client = Mockery::mock('AlgoliaSearch\Client');

        $client->shouldReceive('initIndex')
            ->once()
            ->with($this->organization->slug . '_users')
            ->andReturn($index = Mockery::mock('StdClass'));

        $index->shouldReceive('deleteObjects')
            ->once()
            ->with([
                $users[0]->id,
                $users[1]->id,
            ]);

        $engine = new ExtendedEngine($client);

        $engine->setIndices([
            MockUserIndex::class,
        ]);

        $engine->delete($users);
    }
}
