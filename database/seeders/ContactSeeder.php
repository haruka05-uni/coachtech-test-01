<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Contact;
use App\Models\Category;
use App\Models\Tag;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryIds = Category::pluck('id');
        $tagIds = Tag::pluck('id');

        $contacts = Contact::factory()
            ->count(20)
            ->state(function () use ($categoryIds) {
                return [
                    'category_id' => $categoryIds->random(),
                ];
            })
            ->create();

        foreach ($contacts as $contact) {
            $selectedTagIds = $tagIds
                ->random(random_int(1, 3))
                ->all();

            $contact->tags()->attach($selectedTagIds);
        }
    }
}
