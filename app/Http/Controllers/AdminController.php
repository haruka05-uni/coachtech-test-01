<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use App\Http\Requests\IndexContactRequest;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexContactRequest $request)
    {
        $categories = Category::all();
        $tags = Tag::all();

        $validated = $request->validated();

        $query = Contact::query();

        if (!empty($validated['keyword'])) {
            $query->where(function ($query) use ($validated) {
                $query->where('first_name', 'like', '%' . $validated['keyword'] . '%')
                    ->orWhere('last_name', 'like', '%' . $validated['keyword'] . '%')
                    ->orWhere('email', $validated['keyword']);
            });
        }

        if (!empty($validated['gender'])) {
            $query->where('gender', $validated['gender']);
        }

        if (!empty($validated['category_id'])) {
            $query->where('category_id', $validated['category_id']);
        }

        if (!empty($validated['date'])) {
            $query->whereDate('created_at', $validated['date']);
        }

        $contacts = $query
            ->with(['category', 'tags'])
            ->paginate(7);

        return view('admin.index', compact('categories', 'contacts', 'tags'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        $contact->load(['category', 'tags']);

        return view('admin.show', compact('contact'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $contact = Contact::findOrFail($id);

        $contact->delete();

        return redirect('/admin');
    }
}
