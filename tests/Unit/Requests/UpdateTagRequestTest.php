<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\UpdateTagRequest;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Mockery;
use Tests\TestCase;

class UpdateTagRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_same_name_is_accepted(): void
    {
        $tag = Tag::create([
            'name' => '質問',
        ]);

        $request = new UpdateTagRequest();

        $route = Mockery::mock();

        $route->shouldReceive('parameter')
            ->with('tag', null)
            ->andReturn($tag);

        $request->setRouteResolver(fn() => $route);

        $validator = Validator::make(
            [
                'name' => '質問',
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

        $tag2 = Tag::create([
            'name' => '要望',
        ]);

        $request = new UpdateTagRequest();

        $route = Mockery::mock();

        $route->shouldReceive('parameter')
            ->with('tag', null)
            ->andReturn($tag2);

        $request->setRouteResolver(fn() => $route);

        $validator = Validator::make(
            [
                'name' => '質問',
            ],
            $request->rules()
        );

        $this->assertTrue($validator->fails());
    }
}
