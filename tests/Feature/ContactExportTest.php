<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_contacts_can_be_exported_with_filter(): void
    {
        $user = User::factory()->create();

        $category = Category::create([
            'content' => '商品について',
        ]);

        Contact::factory()->create([
            'category_id' => $category->id,
            'first_name' => '山田',
        ]);

        Contact::factory()->create([
            'category_id' => $category->id,
            'first_name' => '佐藤',
        ]);

        $response = $this->actingAs($user)
            ->get('/contacts/export?keyword=山田');

        $response->assertStatus(200);

        // CSVとして返っているか
        $response->assertHeader(
            'content-type',
            'text/csv; charset=UTF-8'
        );

        // 山田は含まれる
        $this->assertStringContainsString(
            '山田',
            $response->streamedContent()
        );

        // 佐藤は含まれない
        $this->assertStringNotContainsString(
            '佐藤',
            $response->streamedContent()
        );
    }

    public function test_contacts_are_exported_in_latest_order(): void
    {
        $user = User::factory()->create();

        $category = Category::create([
            'content' => '商品について',
        ]);

        Contact::factory()->create([
            'category_id' => $category->id,
            'first_name' => '古い',
            'created_at' => now()->subDays(1),
        ]);

        Contact::factory()->create([
            'category_id' => $category->id,
            'first_name' => '新しい',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->get('/contacts/export');

        $response->assertStatus(200);

        $content = $response->streamedContent();

        // 新しい方が先に出てくるか確認
        $this->assertTrue(
            strpos($content, '新しい') < strpos($content, '古い')
        );
    }
}