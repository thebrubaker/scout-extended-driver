<?php

namespace Tests\Unit\Search;

use App\Instructor;
use App\Organization;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Mock data for testing setup.
     */
    public function setUp()
    {
        parent::setUp();
        $this->organization = factory(Organization::class)->create();
        config(['app.organization_id' => $this->organization->id]);
    }

    /**
     * @test
     */
    public function testIndexCanReturnTheClassOfTheModel()
    {
        $index = new MockUserIndex();

        $modelClass = $index->class();

        $this->assertEquals($modelClass, User::class);
    }

    /**
     * @test
     */
    public function testIndexNameIsPrepended()
    {
        $index = new MockUserIndex();

        $name = $index->getName('organization');

        $this->assertEquals($name, 'organization_users');
    }

    /**
     * @test
     */
    public function testIndexCanTransformModelData()
    {
        $index = new MockUserIndex();

        $user       = factory(User::class)->create([
            'organization_id' => $this->organization->id,
        ]);
        $instructor = factory(Instructor::class)->create([
            'user_id'         => $user->id,
            'organization_id' => $this->organization->id,
        ]);
        // dd($user->load('instructor')->toArray());
        $data = $index->transform($user);

        $this->assertArraySubset([
            'full_name' => $user->first_name . ' ' . $user->last_name,
            'email'     => $user->email,
        ], $data);

        $this->assertFalse(Arr::exists($data, 'password'));
        $this->assertTrue(Arr::exists($data, 'objectID'));
    }

    /**
     * @test
     */
    public function testIndexCanScopeModels()
    {
        $index = new MockUserIndex();

        factory(User::class, 5)->create([
            'organization_id' => $this->organization->id,
        ]);

        factory(User::class, 5)->create([
            'organization_id' => factory(Organization::class)->create()->id,
        ]);

        $index->batchPrepare(10, function ($users) {
            $this->assertTrue($users instanceof Collection);
            $this->assertEquals(5, $users->count());
        });
    }

    /**
     * @test
     */
    public function testIndexReturnsSettings()
    {
        $index = new MockUserIndex();

        $settings = $index->getSettings();

        $this->assertTrue(is_array($settings));
    }
}