<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogoutApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        // テストユーザ作成
        $this->user = factory(User::class)->create();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_認証済みのユーザをログアウトさせる()
    {
        $response = $this->actingAs($this->user)
                        ->json('POST', route('logout'));

        $response->assertStatus(200);
        $this->assertGuest();
    }
}
