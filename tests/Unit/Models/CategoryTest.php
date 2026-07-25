<?php

namespace Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Category;
use App\Models\Contact;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_has_many_contacts(): void
    {
        $category = Category::create([
            'content' => '商品のお届け',
        ]);

        Contact::factory()->count(3)->create([
            'category_id' => $category->id,
        ]);

        $this->assertCount(3, $category->contacts);
    }
}
