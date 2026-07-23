<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use App\Http\Requests\StoreContactRequest;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        $tags = Tag::all();

        return view('contact.index', compact('categories', 'tags'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function confirm(StoreContactRequest $request)
    {
        $validated = $request->validated();

        $category = Category::findOrFail($validated['category_id']);
        $tags = Tag::whereIn('id', $validated['tag_ids'] ?? [])->get();

        return view('contact.confirm', compact('validated', 'category', 'tags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContactRequest $request)
    {
        $validated = $request->validated();
        $tagIds = $validated['tag_ids'] ?? [];

        unset($validated['tag_ids']);

        $contact = Contact::create($validated);

        $contact->tags()->attach($tagIds);

        return view('contact.thanks');
    }
}
