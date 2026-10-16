<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExportContactRequest;
use App\Http\Requests\StoreContactRequest;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;

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

    public function export(ExportContactRequest $request)
    {
        $query = Contact::with('category');

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('first_name', 'like', "%{$keyword}%")
                    ->orWhere('last_name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('gender') && $request->gender != '0') {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $contacts = $query->latest()->get();

        $csvHeader = [
            'ID',
            '氏名',
            '性別',
            'メールアドレス',
            '電話番号',
            '住所',
            '建物名',
            'お問い合わせの種類',
            '内容',
            '作成日時',
        ];

        $csvData = [];

        foreach ($contacts as $contact) {
            $genderLabel = match ((int) $contact->gender) {
                1 => '男性',
                2 => '女性',
                3 => 'その他',
                default => '',
            };

            $csvData[] = [
                $contact->id,
                $contact->last_name.' '.$contact->first_name,
                $genderLabel,
                $contact->email,
                $contact->tel,
                $contact->address,
                $contact->building,
                $contact->category->content ?? '',
                $contact->detail,
                $contact->created_at ? $contact->created_at->format('Y-m-d H:i:s') : '',
            ];
        }

        $callback = function () use ($csvHeader, $csvData) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, $csvHeader);

            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        $filename = 'contacts_'.date('Ymd_His').'.csv';

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
