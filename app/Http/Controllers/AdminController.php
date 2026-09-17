<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Category;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * 管理画面の一覧・検索 (PG05)
     */
    public function index(Request $request)
    {
        $query = Contact::with(['category', 'tags']);

        // 名前（部分一致）
        if ($request->filled('fullname')) {
            $fullname = $request->input('fullname');
            $query->where(function($q) use ($fullname) {
                $q->where('first_name', 'like', "%{$fullname}%")
                  ->orWhere('last_name', 'like', "%{$fullname}%")
                  ->orWhereRaw("CONCAT(first_name, last_name) like ?", ["%{$fullname}%"])
                  ->orWhereRaw("CONCAT(last_name, first_name) like ?", ["%{$fullname}%"]);
            });
        }

        // 性別（1,2,3で絞り込み。0またはallは全て）
        if ($request->filled('gender') && $request->input('gender') !== 'all' && $request->input('gender') !== '0') {
            $query->where('gender', $request->input('gender'));
        }

        // カテゴリID
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // 日付
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->input('date'));
        }

        // 仕様書通り、7件ごとにページネーション表示
        $contacts = $query->latest()->paginate(7)->withQueryString();
        $categories = Category::all();

        return view('admin.index', compact('contacts', 'categories'));
    }

    /**
     * お問い合わせ詳細ページ (PG05-2)
     * 【仕様書準拠】JSON返却ではなく、カテゴリ・タグ情報付きで詳細ページを表示する
     */
    public function show($id)
    {
        // 指定されたお問い合わせをカテゴリ・タグ情報付きで取得
        $contact = Contact::with(['category', 'tags'])->findOrFail($id);

        // 仕様書で指定された詳細ビュー（admin.show）を表示
        return view('admin.show', compact('contact'));
    }

    /**
     * お問い合わせデータの削除
     * 【仕様書準拠】該当データを削除後、/admin にリダイレクトする
     */
    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        // 仕様書通り、削除完了後は管理画面一覧（/admin）にリダイレクト
        return redirect()->route('admin.index')->with('success', 'お問い合わせデータを削除しました。');
    }
}