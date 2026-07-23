<?php

namespace Tests\Unit\Requests;

use Tests\TestCase;
use App\Http\Requests\IndexContactRequest;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IndexContactRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_keyword_is_accepted(): void
    {
        $request = new IndexContactRequest();

        $validator = Validator::make(
            [
                'keyword' => 'laravel',
            ],
            $request->rules()
        );

        $this->assertFalse($validator->fails());
    }

    public function test_gender_is_accepted(): void
    {
        $request = new IndexContactRequest();

        $validator = Validator::make(
            [
                'gender' => 1,
            ],
            $request->rules()
        );

        $this->assertFalse($validator->fails());
    }

    public function test_category_id_is_accepted(): void
    {
        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        $request = new IndexContactRequest();

        $validator = Validator::make(
            [
                'category_id' => $category->id,
            ],
            $request->rules()
        );

        $this->assertFalse($validator->fails());
    }

    public function test_date_is_accepted(): void
    {
        $request = new IndexContactRequest();

        $validator = Validator::make(
            [
                'date' => '2026-07-23',
            ],
            $request->rules()
        );

        $this->assertFalse($validator->fails());
    }

    public function test_invalid_gender_is_rejected(): void
    {
        $request = new IndexContactRequest();

        $validator = Validator::make(
            [
                'gender' => 4,
            ],
            $request->rules()
        );

        $this->assertTrue($validator->fails());
    }
}
