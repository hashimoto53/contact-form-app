<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Contact;

class ContactController extends Controller
{
    /**
     * 1. 入力画面の表示
     */
    public function index()
    {
        // データベースからカテゴリとタグを全件取得して画面へ渡す
        $categories = Category::all();
        $tags = Tag::all();

        return view('contact.index', compact('categories', 'tags'));
    }

    /**
     * 2. 確認画面の表示（POST）
     */
    public function confirm(StoreContactRequest $request)
    {
        $inputs = $request->validated();

        $category = Category::find($inputs['category_id']);
        $selectedTags = isset($inputs['tag_ids']) ? Tag::whereIn('id', $inputs['tag_ids'])->get() : collect();

        return view('contact.confirm', compact('inputs', 'category', 'selectedTags'));
    }

    /**
     * 3. 送信処理（DB保存）
     */
    public function store(StoreContactRequest $request)
    {
        $validated = $request->validated();
        
        $contact = Contact::create($validated);

        if (!empty($validated['tag_ids'])) {
            $contact->tags()->attach($validated['tag_ids']);
        }

        return redirect()->route('contact.thanks');
    }

    /**
     * 4. 完了画面の表示
     */
    public function thanks()
    {
        return view('contact.thanks');
    }
}