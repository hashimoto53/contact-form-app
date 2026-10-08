<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $tags = Tag::all();

        return view('contact.index', compact('categories', 'tags'));
    }

    public function confirm(StoreContactRequest $request)
    {
        // 入力値のバリデーションを実行し、検証済みデータを取得
        $validated = $request->validated();

        // 選択されたカテゴリ（お問い合わせの種類）の情報を取得
        $category = Category::find($validated['category_id']);

        // 選択されたタグを取得（未選択の場合は空の配列を考慮）
        $tags = Tag::whereIn('id', $validated['tag_ids'] ?? [])->get();

        // compact の中に 'tags' を追加して確認画面へ渡します
        return view('contact.confirm', compact('validated', 'category', 'tags'));
    }

    public function store(StoreContactRequest $request)
    {
        // 確認画面で「修正」ボタン（name="back"）が押された場合は入力を保持して入力画面に戻る
        if ($request->has('back')) {
            return redirect()->route('contact.index')->withInput();
        }

        // バリデーション済みのデータを取得
        $validated = $request->validated();

        // データベースへ保存（tag_ids を除外した値を渡す）
        $contact = Contact::create(collect($validated)->except('tag_ids')->all());

        // タグの紐付け（中間テーブル）
        if (isset($validated['tag_ids'])) {
            $contact->tags()->sync($validated['tag_ids']);
        }

        return redirect()->route('contact.thanks');
    }

    public function thanks()
    {
        return view('contact.thanks');
    }
}