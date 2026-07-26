<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tag;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;

class TagController extends Controller
{
    public function store(StoreTagRequest $request)
    {
        $validated = $request->validated();

        Tag::create($validated);

        return redirect('/admin');
    }
    public function edit(Tag $tag)
    {
        return view('admin.tags.edit', compact('tag'));
    }
    public function update(UpdateTagRequest $request, string $id)
    {
        $validated = $request->validated();

        $tag = Tag::findOrFail($id);

        $tag->update($validated);

        return redirect('/admin');
    }

    public function destroy(string $id)
    {
        $tag = Tag::findOrFail($id);

        $tag->delete();

        return redirect('/admin');
    }
}
