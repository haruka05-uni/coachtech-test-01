<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Tag;

class AdminTagTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $tag = Tag::create([
            'name' => '質問',
        ]);

        $response = $this->get('/admin/tags/' . $tag->id . '/edit');

        $response->assertRedirect('/login');
    }

    public function test_tag_crud(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $tag = Tag::create([
            'name' => '質問',
        ]);

        //編集画面表示
        $response = $this->get('/admin/tags/' . $tag->id . '/edit');

        $response->assertViewIs('admin.tags.edit');

        //タグ作成
        $response = $this->post('/admin/tags', [
            'name' => '要望',
        ]);

        $this->assertDatabaseHas('tags', [
            'name' => '要望',
        ]);

        $response->assertRedirect('/admin');

        //更新
        $response = $this->put('/admin/tags/' . $tag->id, [
            'name' => 'その他',
        ]);

        $this->assertDatabaseHas('tags', [
            'name' => 'その他',
        ]);

        $response->assertRedirect('/admin');

        //削除
        $response = $this->delete('/admin/tags/' . $tag->id);

        $this->assertDatabaseMissing('tags', [
            'name' => '質問',
        ]);

        $response->assertRedirect('/admin');
    }
}
