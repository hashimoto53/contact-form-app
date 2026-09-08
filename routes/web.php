<?php

use Illuminate\Support\Facades\Route;
use App\Models\Contact;
use App\Models\Category;
use App\Models\Tag;

Route::get('/', function () {
    // 1. お問い合わせデータ（DBやテーブルが未完成でもエラーにならないようガード）
    try {
        $contacts = Contact::paginate(10);
    } catch (\Throwable $e) {
        $contacts = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
    }

    // 2. カテゴリーデータ（ビューの @foreach 用）
    try {
        $categories = Category::all();
    } catch (\Throwable $e) {
        $categories = collect([]);
    }

    // 3. タグデータ（ビューの @isset($tags) / @forelse 用）
    try {
        $tags = Tag::all();
    } catch (\Throwable $e) {
        $tags = collect([]);
    }

    return view('admin.index', compact('contacts', 'categories', 'tags'));
});