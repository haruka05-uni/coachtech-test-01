<?php

namespace Tests\Unit\Requests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Http\Requests\StoreContactRequest;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\Tag;

class StoreContactRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_required_fields_are_accepted(): void
    {
        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        $request = new StoreContactRequest();

        $validator = Validator::make(
            [
                'first_name' => '久保',
                'last_name' => '遥',
                'gender' => 2,
                'email' => 'test@example.com',
                'tel' => '09000000000',
                'address' => '東京都',
                'category_id' => $category->id,
                'detail' => 'ああああああああああああ',
            ],
            $request->rules()
        );

        $this->assertFalse($validator->fails());
    }

    public function test_tag_ids_are_accepted(): void
    {
        $tag1 = Tag::create([
            'name' => '質問',
        ]);

        $tag2 = Tag::create([
            'name' => '要望',
        ]);

        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        $request = new StoreContactRequest();

        $validator = Validator::make(
            [
                'first_name' => '久保',
                'last_name' => '遥',
                'gender' => 2,
                'email' => 'test@example.com',
                'tel' => '09000000000',
                'address' => '東京都',
                'category_id' => $category->id,
                'detail' => 'ああああああああああああ',
                'tag_ids' => [$tag1->id, $tag2->id],
            ],
            $request->rules()
        );

        $this->assertFalse($validator->fails());
    }

    public function test_invalid_phone_number_is_rejected(): void
    {
        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        $request = new StoreContactRequest();

        $validator = Validator::make(
            [
                'first_name' => '久保',
                'last_name' => '遥',
                'gender' => 2,
                'email' => 'test@example.com',
                'tel' => '090-0000-0000',
                'address' => '東京都',
                'category_id' => $category->id,
                'detail' => 'ああああああああああああ',
            ],
            $request->rules()
        );

        $this->assertTrue($validator->fails());
    }

}
