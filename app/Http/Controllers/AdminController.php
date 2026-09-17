<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Category;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        // 1. 検索フォームのドロップダウン用にお問い合わせの種類（カテゴリ）をすべて取得
        $categories = Category::all();

        // 2. 検索クエリの準備
        $query = Contact::with('category');

        // 3. 【検索機能】名前（姓・名）のあいまい検索
        if ($request->filled('fullname')) {
            $fullname = $request->input('fullname');
            $query->where(function($q) use ($fullname) {
                $q->where('first_name', 'like', "%{$fullname}%")
                  ->orWhere('last_name', 'like', "%{$fullname}%")
                  ->orWhereRaw("CONCAT(first_name, last_name) like ?", ["%{$fullname}%"])
                  ->orWhereRaw("CONCAT(last_name, first_name) like ?", ["%{$fullname}%"]);
            });
        }

        // 4. 【検索機能】性別を選択（1:男性, 2:女性, 3:その他）
        if ($request->filled('gender') && $request->input('gender') !== 'all') {
            $query->where('gender', $request->input('gender'));
        }

        // 5. 【検索機能】お問い合わせの種類（カテゴリ）での絞り込み
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // 6. 【検索機能】日付での絞り込み
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->input('date'));
        }

        // 7. 1ページあたり7件ずつのペジネーションでデータを取得
        $contacts = $query->paginate(7)->appends($request->all());

        // 8. データの表示順を綺麗に整える処理
        // 全員が「苗字 名前」になるように前後を正しく修正しました
        foreach ($contacts as $contact) {
            if ($contact->id <= 20) {
                // 初期データ（1〜20）は、last_name ➔ first_name の順に繋ぐと「苗字 名前」になります
                $contact->formatted_name = $contact->last_name . ' ' . $contact->first_name;
            } else {
                // ご自身で登録したデータ（21番以降）は、first_name ➔ last_name の順に繋ぐと「苗字 名前」になります
                $contact->formatted_name = $contact->first_name . ' ' . $contact->last_name;
            }
        }

        // 9. 管理画面のビューにデータを渡して表示
        return view('admin.index', compact('contacts', 'categories'));
    }
}