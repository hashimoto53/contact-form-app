<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use App\Http\Requests\ExportContactRequest;

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
            'status'  => 'お問い合わせデータを削除しました。',
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

        $contacts = $query->get();

        $csvHeader = ['お名前', '性別', 'メールアドレス', 'お問い合わせの種類', '詳細'];
        $csvData = [];

        foreach ($contacts as $contact) {
            $genderLabel = match ((int)$contact->gender) {
                1 => '男性',
                2 => '女性',
                3 => 'その他',
                default => '',
            };

            $csvData[] = [
                $contact->last_name . ' ' . $contact->first_name,
                $genderLabel,
                $contact->email,
                $contact->category->content ?? '',
                $contact->detail,
            ];
        }

        $callback = function () use ($csvHeader, $csvData) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, $csvHeader);

            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        $filename = 'contacts_' . date('Ymd_His') . '.csv';

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}