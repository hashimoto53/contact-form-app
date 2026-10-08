<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexContactRequest;
use App\Http\Requests\ExportContactRequest;
use App\Models\Category;
use App\Models\Contact;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    /**
     * 管理画面の一覧・検索 (PG05)
     */
    public function index(IndexContactRequest $request)
    {
        $query = Contact::with(['category', 'tags']);

        // キーワード検索（姓・名・メールアドレスの部分一致）
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('first_name', 'like', "%{$keyword}%")
                  ->orWhere('last_name', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%")
                  ->orWhereRaw('CONCAT(first_name, last_name) like ?', ["%{$keyword}%"])
                  ->orWhereRaw('CONCAT(last_name, first_name) like ?', ["%{$keyword}%"]);
            });
        }

        // 性別検索（0は全選択、1:男性, 2:女性, 3:その他）
        if ($request->filled('gender') && $request->gender != 0) {
            $query->where('gender', $request->gender);
        }

        // カテゴリID絞り込み
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // 日付検索
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // 7件ごとにページネーション（検索条件をURLに保持）
        $contacts = $query->latest()->paginate(7)->withQueryString();
        $categories = Category::all();

        return view('admin.index', compact('contacts', 'categories'));
    }

    /**
     * お問い合わせ詳細ページ (PG05-2)
     */
    public function show($id)
    {
        $contact = Contact::with(['category', 'tags'])->findOrFail($id);

        return view('admin.show', compact('contact'));
    }

    /**
     * お問い合わせデータの削除
     */
    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return redirect()->route('admin.index')->with('success', 'お問い合わせデータを削除しました。');
    }

    /**
     * CSVエクスポート処理
     */
    public function export(ExportContactRequest $request): StreamedResponse
    {
        $query = Contact::with('category');

        // 検索条件の適用
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('first_name', 'like', "%{$keyword}%")
                  ->orWhere('last_name', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('gender') && $request->gender != 0) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $contacts = $query->latest()->get();

        $response = new StreamedResponse(function () use ($contacts) {
            $stream = fopen('php://output', 'w');

            // Excel文字化け防止のためのBOM書き込み
            fwrite($stream, "\xEF\xBB\xBF");

            // ヘッダー行の出力
            fputcsv($stream, [
                'ID',
                '氏名',
                '性別',
                'メールアドレス',
                '電話番号',
                '住所',
                '建物名',
                'お問い合わせの種類',
                'お問い合わせ内容',
                '作成日時',
            ]);

            // 性別の数値変換マップ
            $genderMap = [1 => '男性', 2 => '女性', 3 => 'その他'];

            // データ行の出力
            foreach ($contacts as $contact) {
                fputcsv($stream, [
                    $contact->id,
                    $contact->first_name . ' ' . $contact->last_name,
                    $genderMap[$contact->gender] ?? '不明',
                    $contact->email,
                    $contact->tel,
                    $contact->address,
                    $contact->building,
                    $contact->category->content ?? '',
                    $contact->detail,
                    $contact->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($stream);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="contacts_' . date('Ymd_His') . '.csv"');

        return $response;
    }
}