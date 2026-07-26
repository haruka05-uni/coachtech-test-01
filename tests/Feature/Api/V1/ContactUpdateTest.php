<?php

namespace Tests\Feature\Api\V1;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_is_updated(): void
    {
        $category1 = Category::create([
            'content' => '商品について',
        ]);

        $category2 = Category::create([
            'content' => '返品について',
        ]);

        $tag = Tag::create([
            'name' => '重要',
        ]);

        $contact = Contact::factory()->create([
            'category_id' => $category1->id,
            'first_name' => '太郎',
            'email' => 'test@example.com',
        ]);

        $response = $this->putJson("/api/v1/contacts/{$contact->id}", [
            'first_name' => '次郎',
            'last_name' => $contact->last_name,
            'gender' => $contact->gender,
            'email' => 'updated@example.com',
            'tel' => $contact->tel,
            'address' => $contact->address,
            'building' => $contact->building,
            'category_id' => $category2->id,
            'detail' => '更新しました',
            'tag_ids' => [$tag->id],
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'first_name' => '次郎',
            'email' => 'updated@example.com',
            'category_id' => $category2->id,
            'detail' => '更新しました',
        ]);

        $this->assertDatabaseHas('contact_tag', [
            'contact_id' => $contact->id,
            'tag_id' => $tag->id,
        ]);
    }

    public function test_not_found_returns_404(): void
    {
        $response = $this->putJson('/api/v1/contacts/999999', []);

        $response->assertStatus(404);

        $response->assertExactJson([
            'error' => 'お問い合わせが見つかりませんでした。',
        ]);
    }

    public function test_validation_error_returns_422(): void
    {
        $category = Category::create([
            'content' => '商品について',
        ]);

        $contact = Contact::factory()->create([
            'category_id' => $category->id,
        ]);

        $response = $this->putJson("/api/v1/contacts/{$contact->id}", [
            'first_name' => '',
            'last_name' => '',
            'gender' => 4,
            'email' => 'abc',
            'tel' => '123',
            'address' => '',
            'category_id' => 999999,
            'detail' => '',
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'first_name',
            'last_name',
            'gender',
            'email',
            'tel',
            'address',
            'category_id',
            'detail',
        ]);
    }
}