<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Branch;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class BranchTest extends TestCase
{
    use WithoutMiddleware;
    /**
     * A basic test example.
     *
     * @return void
     */

    public function testCreate()
    {
        $branches = factory(Branch::class, 10)->create();
        $data = [
            'branch_name' => 'Test Branch',
        ];
        $response = $this->json('POST', route('branch.create'), $data);
        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success'
            ]);
        Branch::truncate();
    }

    public function testViewOne()
    {
        $branches = factory(Branch::class, 20)->create();

        $testId = rand(1, 20);

        $response = $this->json('GET', route('branch.view', ['id' => $testId]));

        $response->assertStatus(200);

        Branch::truncate();
    }

    public function testUpdate()
    {
        $branches = factory(Branch::class, 20)->create();

        $testId = rand(1, 20);

        $data = [
            'branch_name' => 'Test Branch Updated Name',
        ];

        $response = $this->json('PATCH', route('branch.update', ['id' => $testId]), $data);
        $response->assertStatus(200);
        Branch::truncate();
    }

    public function testMove()
    {
        $branches = factory(Branch::class, 10)->create();

        $response = $this->json('POST', route('branch.move', ['id' => 2, 'toBranchId' => 1]));
        $response->assertStatus(200);
        Branch::truncate();
    }

    public function testMoveCantBeSelfError()
    {
        $branches = factory(Branch::class, 10)->create();

        $response = $this->json('POST', route('branch.move', ['id' => 3, 'toBranchId' => 3]));

        $response->assertStatus(422);

        Branch::truncate();
    }

    public function testMoveCirleError()
    {
        $branches = factory(Branch::class, 10)->create();

        $this->json('POST', route('branch.move', ['id' => 1, 'toBranchId' => 3]));
        $this->json('POST', route('branch.move', ['id' => 4, 'toBranchId' => 1]));
        $this->json('POST', route('branch.move', ['id' => 8, 'toBranchId' => 4]));
        $response = $this->json('POST', route('branch.move', ['id' => 3, 'toBranchId' => 8]));

        $response->assertStatus(422);

        Branch::truncate();
    }


    public function testMultipleMove()
    {
        $branches = factory(Branch::class, 20)->create();

        $this->json('POST', route('branch.move', ['id' => 1, 'toBranchId' => 3]));
        $this->json('POST', route('branch.move', ['id' => 4, 'toBranchId' => 3]));
        $this->json('POST', route('branch.move', ['id' => 5, 'toBranchId' => 4]));
        $this->json('POST', route('branch.move', ['id' => 6, 'toBranchId' => 4]));
        $this->json('POST', route('branch.move', ['id' => 10, 'toBranchId' => 6]));
        $this->json('POST', route('branch.move', ['id' => 7, 'toBranchId' => 9]));
        $this->json('POST', route('branch.move', ['id' => 9, 'toBranchId' => 1]));

        $this->json('POST', route('branch.move', ['id' => 11, 'toBranchId' => 4]));
        $response1 = $this->json('POST', route('branch.move', ['id' => 12, 'toBranchId' => 11]));
        $this->json('POST', route('branch.move', ['id' => 13, 'toBranchId' => 11]));
        $this->json('POST', route('branch.move', ['id' => 14, 'toBranchId' => 12]));
        $this->json('POST', route('branch.move', ['id' => 15, 'toBranchId' => 12]));
        $this->json('POST', route('branch.move', ['id' => 16, 'toBranchId' => 10]));
        $response2 = $this->json('POST', route('branch.move', ['id' => 17, 'toBranchId' => 10]));

        $response1->assertStatus(200);
        $response2->assertStatus(200);

        $response = $this->json('GET', route('branch.viewWithChildren', ['id' => 4]));
        $jsonContent = $response->getContent();
        $contentArr = json_decode($jsonContent, true);
        $data = $contentArr['data'];

        return $data;
    }

    /**
     * @depends testMultipleMove
     */
    public function testViewAll()
    {
        $response = $this->json('GET', route('branch.index'));

        $response->assertStatus(200);
    }

    /**
     * @depends testMultipleMove
     */
    public function testViewOneWithAllChildren(array $data)
    {
        $hasRoot = false;
        $hasCertainNode = false;

        foreach ($data as $key => $node) {
            if ($node['id'] === 4) {
                $this->assertEquals(0, $key);
                $this->assertEquals(0, $node['level']);
                $hasRoot = true;
            }

            if ($node['id'] === 15) {
                $this->assertEquals(3, $node['level']);
                $hasCertainNode = true;
            }
        }

        if (!$hasRoot) {
            $this->assertTrue(false);
        }

        if (!$hasCertainNode) {
            $this->assertTrue(false);
        }

        $this->assertEquals(11, sizeof($data));
    }

    /**
     * @depends testMultipleMove
     */
    public function testDelete(array $data)
    {

        $response = $this->json('DELETE', route('branch.delete', ['id' => 4]));

        $response->assertStatus(204);

        $this->assertDatabaseMissing('branches', [
            'id' => 10
        ]);

        $this->assertDatabaseMissing('branches', [
            'id' => 16
        ]);

        $this->assertDatabaseMissing('branches', [
            'id' => 11
        ]);
        // Branch::truncate();
    }
}
