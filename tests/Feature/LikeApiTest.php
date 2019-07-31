<?php

namespace Tests\Feature;

use App\Photo;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LikeApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();

        factory(Photo::class)->create();
        $this->photo = Photo::first();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_いいねを追加できる()
    {
        $response = $this->actingAs($this->user)
            ->json('PUT', route('photo.like', [
                'photo' => $this->photo->id,
            ]));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'photo_id' => $this->photo->id,
            ]);

        $this->assertEquals(1, $this->photo->likes()->count());
    }

    public function test_2回同じ写真にいいねしても1個しかいいねがつかない()
    {
        $param = ['id' => $this->photo->id];
        $this->actingAs($this->user)->json('PUT', route('photo.like', $param));
        $this->actingAs($this->user)->json('PUT', route('photo.like', $param));

        $this->assertEquals(1, $this->photo->likes()->count());
    }

    public function test_いいねを解除できる()
    {
        $this->photo->likes()->attach($this->user->id);

        $response = $this->actingAs($this->user)
            ->json('DELETE', route('photo.like', [
                'photo' => $this->photo->id,
            ]));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'photo_id' => $this->photo->id,
            ]);

        $this->assertEquals(0, $this->photo->likes()->count());
    }
}
