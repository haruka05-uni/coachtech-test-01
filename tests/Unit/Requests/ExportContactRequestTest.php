<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\ExportContactRequest;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ExportContactRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_filters_are_accepted(): void
    {
        $category = Category::create([
            'content' => '商品について',
        ]);

        $request = new ExportContactRequest();

        $validator = Validator::make(
            [
                'keyword' => '山田',
                'gender' => 1,
                'category_id' => $category->id,
                'date' => '2026-07-26',
            ],
            $request->rules()
        );

        $this->assertFalse($validator->fails());
    }

    public function test_invalid_filters_are_rejected(): void
    {
        $request = new ExportContactRequest();

        $validator = Validator::make(
            [
                'gender' => 5,
                'category_id' => 999999,
                'date' => 'invalid-date',
            ],
            $request->rules()
        );

        $this->assertTrue($validator->fails());

        $this->assertTrue($validator->errors()->has('gender'));
        $this->assertTrue($validator->errors()->has('category_id'));
        $this->assertTrue($validator->errors()->has('date'));
    }
}