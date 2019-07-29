<?php

namespace Tests\Feature;

use App\Photo;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class PhotoSubmitApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_ファイルをアップロードする()
    {
        Storage::fake('s3');

        $response = $this->actingAs('photo.create')
            ->json('POST', route('photo.create'), [
                'photo' => UploadedFile::fake()->image('photo.jpg')
            ]);

        $response->assertStatus(201);

        $photo = Photo::first();

        $this->assertRegExp('/^[0-9a-zA-Z-_]{12}$/', $photo->id);

        Storage::cloud()->assertExists($photo->filename);
    }

    public function test_データベースエラーの場合はファイルを保存しない()
    {
        Schema::drop('photos');

        Storage::fake('s3');

        $response = $this->actingAs($this->user)
            ->json('POST', route('photo.create'), [
                'photo' => UploadedFile::fake()->image('photo.jpg'),
            ]);

        $response->assertStatus(500);

        $this->assertEquals(0, count(Storage::cloud()->files()));
    }

    public function test_ファイル保存エラーの場合はDBへの挿入はしない()
    {
        // ストレージをモックして保存時にエラーを起こさせる
        Storage::shouldReceive('cloud')
            ->once()
            ->andReturnNull();

        $response = $this->actingAs($this->user)
            ->json('POST', route('photo.create'), [
                'photo' => UploadedFile::fake()->image('photo.jpg'),
            ]);

        // レスポンスが500(INTERNAL SERVER ERROR)であること
        $response->assertStatus(500);

        // データベースに何も挿入されていないこと
        $this->assertEmpty(Photo::all());
    }
}
