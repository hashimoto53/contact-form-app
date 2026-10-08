<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExportContactRequest;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Contact::with(['category', 'tags']);

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

        if ($request->filled('gender') && $request->gender != '0') {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $contacts = $query->latest()->paginate(7)->withQueryString();
        $categories = Category::all();
        $tags = Tag::all();

        return view('admin.index', compact('contacts', 'categories', 'tags'));
    }

    public function show($id)
    {
        $contact = Contact::with(['category', 'tags'])->findOrFail($id);

        return view('admin.show', compact('contact'));
    }

    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return redirect('/admin')->with([
            'message' => 'お問い合わせデータを削除しました。',
            'success' => 'お問い合わせデータを削除しました。',
            'status' => 'お問い合わせデータを削除しました。',
        ]);
    }

    public function export(ExportContactRequest $request)
    {
        $query = Contact::with('category');

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
            '詳細',
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
