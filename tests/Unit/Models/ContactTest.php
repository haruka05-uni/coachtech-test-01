<?php

namespace Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Contact;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_belongs_to_category(): void
    {
        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        $contact = Contact::factory()->create([
            'category_id' => $category->id,
        ]);

        $this->assertEquals($category->id, $contact->category->id);
    }

    public function test_contact_can_sync_tags(): void
    {
        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        $contact = Contact::factory()->create([
            'category_id' => $category->id
        ]);

        $tag1 = Tag::create([
            'name' => '質問',
        ]);

        $tag2 = Tag::create([
            'name' => '要望',
        ]);

        $contact->tags()->sync([
            $tag1->id,
            $tag2->id,
        ]);

        $this->assertCount(2, $contact->tags);
    }
}
