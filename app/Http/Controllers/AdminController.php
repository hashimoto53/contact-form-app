<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Category;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * 管理画面の一覧表示 兼 検索処理
     */
    public function index(Request $request)
    {
        $categories = Category::all();
        $query = Contact::with('category');

        // 【検索】名前（姓・名）のあいまい検索
        if ($request->filled('fullname')) {
            $fullname = $request->input('fullname');
            $query->where(function($q) use ($fullname) {
                $q->where('first_name', 'like', "%{$fullname}%")
                  ->orWhere('last_name', 'like', "%{$fullname}%")
                  ->orWhereRaw("CONCAT(first_name, last_name) like ?", ["%{$fullname}%"])
                  ->orWhereRaw("CONCAT(last_name, first_name) like ?", ["%{$fullname}%"]);
            });
        }

        // 【検索】性別の選択（1:男性, 2:女性, 3:その他）
        if ($request->filled('gender') && $request->input('gender') !== 'all') {
            $query->where('gender', $request->input('gender'));
        }

        // 【検索】お問い合わせの種類（カテゴリ）での絞り込み
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // 【検索】日付での絞り込み
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->input('date'));
        }

        // 1ページあたり7件ずつのペジネーションでデータを取得
        $contacts = $query->paginate(7)->appends($request->all());

        // データの表示順を綺麗に整える処理
        foreach ($contacts as $contact) {
            if ($contact->id <= 20) {
                $contact->formatted_name = $contact->last_name . ' ' . $contact->first_name;
            } else {
                $contact->formatted_name = $contact->first_name . ' ' . $contact->last_name;
            }
        }

        return view('admin.index', compact('contacts', 'categories'));
    }

    /**
     * 【新規追加】指定されたお問い合わせの詳細データをJSON形式で返す処理（モーダル用）
     */
    public function show($id)
    {
        // カテゴリとタグの情報も合わせて取得
        $contact = Contact::with(['category', 'tags'])->findOrFail($id);
        
        // 名前の表示順の補正
        if ($contact->id <= 20) {
            $contact->formatted_name = $contact->last_name . ' ' . $contact->first_name;
        } else {
            $contact->formatted_name = $contact->first_name . ' ' . $contact->last_name;
        }

        // 性別の数値をテキストに変換
        $genderLabels = [1 => '男性', 2 => '女性', 3 => 'その他'];
        $contact->gender_label = $genderLabels[$contact->gender] ?? '不明';

        // 作成日時のフォーマット
        $contact->formatted_date = $contact->created_at->format('Y-m-to H:i');

        return response()->json($contact);
    }

    /**
     * 【新規追加】指定されたお問い合わせデータをデータベースから削除する処理
     */
    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        
        // 中間テーブル（tags）との紐付けを安全に解除してから本体を削除
        $contact->tags()->detach();
        $contact->delete();

        return redirect()->route('admin.index')->with('success', 'お問い合わせデータを削除しました。');
    }
}