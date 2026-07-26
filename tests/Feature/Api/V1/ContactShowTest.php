<?php

namespace Tests\Feature\Api\V1;

use App\Models\Category;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_detail_is_returned(): void
    {
        $category = Category::create([
            'content' => '商品について',
        ]);

        $contact = Contact::factory()->create([
            'category_id' => $category->id,
            'first_name' => '太郎',
            'last_name' => '山田',
        ]);

        $response = $this->getJson("/api/v1/contacts/{$contact->id}");

        $response->assertOk();

        $response->assertJsonPath('data.id', $contact->id);
        $response->assertJsonPath('data.first_name', '太郎');
        $response->assertJsonPath('data.last_name', '山田');
    }

    public function test_not_found_returns_404(): void
    {
        $response = $this->getJson('/api/v1/contacts/999999');

        $response->assertStatus(404);

        $response->assertExactJson([
            'error' => 'お問い合わせが見つかりませんでした。',
        ]);
    }
}