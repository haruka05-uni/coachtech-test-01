<?php

namespace Tests\Feature\Contact;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Category;
use App\Models\Tag;
use App\Models\contact;

class ContactStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_is_stored_successfully(): void
    {
        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        $tag = Tag::create([
            'name' => '質問',
        ]);

        $response = $this->post('/contacts', [
            'first_name' => '久保',
            'last_name' => '遥',
            'gender' => 2,
            'email' => 'test@example.com',
            'tel' => '09000000000',
            'address' => '東京都',
            'category_id' => $category->id,
            'detail' => 'ああああああああああああ',
            'tag_ids' => [$tag->id],
        ]);

        $response->assertRedirect('/thanks');

        $this->assertDatabaseHas('contacts', [
            'first_name' => '久保',
            'last_name' => '遥',
            'gender' => 2,
            'email' => 'test@example.com',
            'tel' => '09000000000',
            'address' => '東京都',
            'category_id' => $category->id,
            'detail' => 'ああああああああああああ',
        ]);

        $contact = Contact::where('email', 'test@example.com')->firstOrFail();

        $this->assertDatabaseHas('contact_tag', [
            'contact_id' => $contact->id,
            'tag_id' => $tag->id,
        ]);
    }

    public function test_validation_errors_redirect_back(): void
    {
        $response = $this->from('/contacts')->post('/contacts', []);

        $response->assertRedirect('/contacts');

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
