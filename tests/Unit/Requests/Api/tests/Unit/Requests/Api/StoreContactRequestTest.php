<?php

namespace Tests\Unit\Requests\Api;

use App\Http\Requests\Api\V1\StoreContactRequest;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreContactRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_data_is_accepted(): void
    {
        $category = Category::create([
            'content' => '商品について',
        ]);

        $tag = Tag::create([
            'name' => '重要',
        ]);

        $request = new StoreContactRequest();

        $validator = Validator::make(
            [
                'first_name' => '太郎',
                'last_name' => '山田',
                'gender' => 1,
                'email' => 'test@example.com',
                'tel' => '09012345678',
                'address' => '東京都',
                'building' => 'テストビル',
                'category_id' => $category->id,
                'detail' => 'テスト内容',
                'tag_ids' => [$tag->id],
            ],
            $request->rules()
        );

        $this->assertFalse($validator->fails());
    }

    public function test_invalid_data_is_rejected(): void
    {
        $request = new StoreContactRequest();

        $validator = Validator::make(
            [
                'first_name' => '',
                'last_name' => '',
                'gender' => 5,
                'email' => 'invalid',
                'tel' => 'abc',
                'address' => '',
                'category_id' => 999999,
                'detail' => '',
                'tag_ids' => [999999],
            ],
            $request->rules()
        );

        $this->assertTrue($validator->fails());

        $this->assertTrue($validator->errors()->has('first_name'));
        $this->assertTrue($validator->errors()->has('last_name'));
        $this->assertTrue($validator->errors()->has('gender'));
        $this->assertTrue($validator->errors()->has('email'));
        $this->assertTrue($validator->errors()->has('tel'));
        $this->assertTrue($validator->errors()->has('address'));
        $this->assertTrue($validator->errors()->has('category_id'));
        $this->assertTrue($validator->errors()->has('detail'));
        $this->assertTrue($validator->errors()->has('tag_ids.0'));
    }
}