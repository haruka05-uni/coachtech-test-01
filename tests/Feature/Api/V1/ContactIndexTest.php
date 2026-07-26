<?php

namespace Tests\Feature\Api\V1;

use App\Models\Category;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_list_is_returned(): void
    {
        $category = Category::create([
            'content' => '商品について',
        ]);

        Contact::factory()->count(3)->create([
            'category_id' => $category->id,
        ]);

        $response = $this->getJson('/api/v1/contacts');

        $response->assertOk();

        $response->assertJsonCount(3, 'data');

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'category',
                    'first_name',
                    'last_name',
                    'gender',
                    'email',
                    'tel',
                    'address',
                    'building',
                    'detail',
                    'tags',
                    'created_at',
                    'updated_at',
                ],
            ],
            'links',
            'meta',
        ]);
    }

    public function test_contacts_can_be_searched(): void
    {
        $category1 = Category::create([
            'content' => '商品について',
        ]);

        $category2 = Category::create([
            'content' => '返品について',
        ]);

        Contact::factory()->create([
            'category_id' => $category1->id,
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'yamada@example.com',
            'created_at' => '2026-07-01 10:00:00',
        ]);

        Contact::factory()->create([
            'category_id' => $category2->id,
            'first_name' => '佐藤',
            'last_name' => '花子',
            'gender' => 2,
            'email' => 'sato@example.com',
            'created_at' => '2026-07-02 10:00:00',
        ]);

        Contact::factory()->create([
            'category_id' => $category1->id,
            'first_name' => '鈴木',
            'last_name' => '次郎',
            'gender' => 3,
            'email' => 'suzuki@example.com',
            'created_at' => '2026-07-03 10:00:00',
        ]);

        // キーワード検索
        $response = $this->getJson(
            '/api/v1/contacts?keyword=山田'
        );

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath(
            'data.0.email',
            'yamada@example.com'
        );

        // 性別検索
        $response = $this->getJson(
            '/api/v1/contacts?gender=2'
        );

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath(
            'data.0.email',
            'sato@example.com'
        );

        // カテゴリ検索
        $response = $this->getJson(
            '/api/v1/contacts?category_id=' . $category1->id
        );

        $response->assertOk();
        $response->assertJsonCount(2, 'data');

        // 日付検索
        $response = $this->getJson(
            '/api/v1/contacts?date=2026-07-03'
        );

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath(
            'data.0.email',
            'suzuki@example.com'
        );
    }

    public function test_contacts_are_paginated(): void
    {
        $category = Category::create([
            'content' => '商品について',
        ]);

        Contact::factory()->count(8)->create([
            'category_id' => $category->id,
        ]);

        $response = $this->getJson(
            '/api/v1/contacts?per_page=7'
        );

        $response->assertOk();

        $response->assertJsonCount(7, 'data');

        $response->assertJsonPath('meta.current_page', 1);
        $response->assertJsonPath('meta.per_page', 7);
        $response->assertJsonPath('meta.total', 8);
        $response->assertJsonPath('meta.last_page', 2);
    }

    public function test_invalid_query_returns_422(): void
    {
        $response = $this->getJson(
            '/api/v1/contacts?gender=4&per_page=101'
        );

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'gender',
            'per_page',
        ]);
    }
}