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
        $inputs = $request->validated();
        
        $category = Category::find($inputs['category_id']);
        $selectedTags = !empty($inputs['tag_ids']) 
            ? Tag::whereIn('id', $inputs['tag_ids'])->get() 
            : collect();

        return view('contact.confirm', compact('inputs', 'category', 'selectedTags'));
    }

    public function store(Request $request)
    {
        // 【要件追加】確認画面で「修正」ボタン（name="back"）が押された場合は、入力を保持して入力画面に戻します
        if ($request->has('back')) {
            return redirect()->route('contact.index')->withInput();
        }

        // データベースへ保存
        $contact = Contact::create([
            'first_name'  => $request->first_name,
            'last_name'   => $request->last_name,
            'gender'      => $request->gender,
            'email'       => $request->email,
            'tel'         => $request->tel,
            'address'     => $request->address,
            'building'    => $request->building,
            'category_id' => $request->category_id,
            'detail'      => $request->detail,
        ]);

        // タグの紐付け（中間テーブル）
        if ($request->has('tag_ids')) {
            $contact->tags()->sync($request->tag_ids);
        }

        return redirect()->route('contact.thanks');
    }

    public function thanks()
    {
        return view('contact.thanks');
    }
}