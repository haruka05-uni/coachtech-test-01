<?php

namespace Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Http\Requests\StoreContactRequest;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Contact;


class TagTest extends TestCase
{
    use RefreshDatabase;

    public function test_tag_belongs_to_many_contacts(): void
    {
        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        $tag = Tag::create([
            'name' => '質問',
        ]);

        $contacts = Contact::factory()->count(3)->create([
            'category_id' => $category->id,
        ]);

        foreach ($contacts as $contact) {
            $contact->tags()->sync([$tag->id]);
        }

        $this->assertCount(3, $tag->contacts);
    }
}
