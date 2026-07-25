<?php

namespace Tests\Feature\Contact;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Category;
use App\Models\Tag;

class ContactConfirmTest extends TestCase
{
    use RefreshDatabase;

    public function test_confirm_page_is_displayed(): void
    {
        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        $tag = Tag::create([
            'name' => '質問',
        ]);
        $data = [
            'first_name' => '久保',
            'last_name' => '遥',
            'gender' => 2,
            'email' => 'test@example.com',
            'tel' => '09000000000',
            'address' => '東京都',
            'category_id' => $category->id,
            'detail' => 'ああああああああああああ',
            'tag_ids' => [$tag->id],
        ];

        $response = $this->post('/contacts/confirm', $data);

        $response->assertStatus(200);
        $response->assertViewIs('contact.confirm');

        $response->assertSee($data['first_name']);
        $response->assertSee($data['last_name']);
        $response->assertSee($data['email']);
        $response->assertSee($data['detail']);
    }

    public function test_validation_errors_redirect_back(): void
    {
        $response = $this->from('/')->post('/contacts/confirm', []);

        $response->assertRedirect('/');

        $response->assertSessionHasErrors([
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
