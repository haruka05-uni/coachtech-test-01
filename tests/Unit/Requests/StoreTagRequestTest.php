<?php

namespace Tests\Unit\Requests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Http\Requests\StoreTagRequest;
use Illuminate\Support\Facades\Validator;
use App\Models\Tag;

class StoreTagRequestTest extends TestCase
{
    use RefreshDatabase;
    public function test_name_is_required(): void
    {
        $request = new StoreTagRequest();

        $validator = Validator::make(
            [
                'name' => '',
            ],
            $request->rules()
        );

        $this->assertTrue($validator->fails());
    }

    public function test_name_max_length_is_accepted(): void
    {
        $request = new StoreTagRequest();

        $validator = Validator::make(
            [
                'name' => str_repeat('あ', 50),
            ],
            $request->rules()
        );

        $this->assertFalse($validator->fails());
    }

    public function test_duplicate_name_is_rejected(): void
    {
        $tag1 = Tag::create([
            'name' => '質問',
        ]);

        $request = new StoreTagRequest();

        $validator = Validator::make(
            [
                'name' => '質問',
            ],
            $request->rules()
        );

        $this->assertTrue($validator->fails());
    }
}
