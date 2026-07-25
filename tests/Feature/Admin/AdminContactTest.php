<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Contact;
use App\Models\Category;

class AdminContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin');

        $response->assertredirect('/login');
    }

    public function test_search_and_pagination_work(): void
    {
        // 認証
        $user = User::factory()->create();
        $this->actingAs($user);

        // カテゴリ作成
        $category1 = Category::create([
            'content' => '商品のお届け',
        ]);

        $category2 = Category::create([
            'content' => 'その他',
        ]);

        // 検索用データ①
        Contact::factory()->create([
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'category_id' => $category1->id,
            'created_at' => '2026-07-01 10:00:00',
        ]);

        // 検索用データ②
        Contact::factory()->create([
            'first_name' => '佐藤',
            'last_name' => '花子',
            'gender' => 2,
            'category_id' => $category2->id,
            'created_at' => '2026-07-02 10:00:00',
        ]);

        // ページネーション確認用（合計10件になる）
        Contact::factory()->count(8)->create([
            'category_id' => $category1->id,
        ]);

        // 認証済みユーザーが管理画面を表示できる
        $response = $this->get('/admin');

        $response->assertStatus(200);

        // キーワード検索
        $response = $this->get('/admin?keyword=山田');

        $response->assertStatus(200);
        $response->assertSee('山田');
        $response->assertDontSee('佐藤');

        // 性別検索
        $response = $this->get('/admin?gender=2');

        $response->assertStatus(200);
        $response->assertSee('佐藤');
        $response->assertDontSee('山田');

        // カテゴリ検索
        $response = $this->get('/admin?category_id=' . $category2->id);

        $response->assertStatus(200);
        $response->assertSee('佐藤');
        $response->assertDontSee('山田');

        // 日付検索
        $response = $this->get('/admin?date=2026-07-01');

        $response->assertStatus(200);
        $response->assertSee('山田');
        $response->assertDontSee('佐藤');

        // ページネーション（1ページ7件）
        $response = $this->get('/admin');

        $response->assertStatus(200);
        $response->assertViewHas('contacts', function ($contacts) {
            return $contacts->count() === 7;
        });
    }
    public function test_contact_detail_is_displayed(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $category = Category::create([
            'content' => '商品のお届け',
        ]);

        $contact = Contact::factory()->create([
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'category_id' => $category->id,
            'created_at' => '2026-07-01 10:00:00',
        ]);

        $response = $this->get('/admin/contacts/' . $contact->id);

        $response->assertStatus(200);
        $response->assertViewIs('admin.show');
        $response->assertSee('山田');
        $response->assertSee('太郎');
        $response->assertSee('商品のお届け');
    }

    public function test_contact_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $category = Category::create([
            'content' => '商品のお届け',
        ]);

        $contact = Contact::factory()->create([
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'category_id' => $category->id,
            'created_at' => '2026-07-01 10:00:00',
        ]);

        $response = $this->from('/admin')->delete('/admin/contacts/' . $contact->id);

        $this->assertDatabaseMissing('contacts', [
            'id' => $contact->id,
        ]);

        $response->assertRedirect('/admin');
    }
}
