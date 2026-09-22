<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    /**
     * お問い合わせデータをCSV形式でダウンロードする処理
     */
    public function export(Request $request)
    {
        // 1. 検索条件を引き継いでデータを絞り込む
        $query = Contact::with(['category', 'tags']);

        if ($request->filled('fullname')) {
            $fullname = $request->input('fullname');
            $query->where(function ($q) use ($fullname) {
                $q->where('first_name', 'like', "%{$fullname}%")
                    ->orWhere('last_name', 'like', "%{$fullname}%")
                    ->orWhereRaw('CONCAT(first_name, last_name) like ?', ["%{$fullname}%"])
                    ->orWhereRaw('CONCAT(last_name, first_name) like ?', ["%{$fullname}%"]);
            });
        }

        if ($request->filled('gender') && $request->input('gender') !== 'all') {
            $query->where('gender', $request->input('gender'));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->input('date'));
        }

        // 2. 検索条件に一致するすべてのデータを取得
        $contacts = $query->get();

        // 3. ストリーミング（ダウンロード用）のレスポンスを作成
        $response = new StreamedResponse(function () use ($contacts) {
            $stream = fopen('php://output', 'w');

            // Excelで文字化けしないようにBOM（日本語コードの目印）を追加
            fwrite($stream, pack('C*', 0xEF, 0xBB, 0xBF));

            // CSVの1行目（ヘッダー項目）を書き込む
            fputcsv($stream, ['ID', 'お名前', '性別', 'メールアドレス', '電話番号', '住所', '建物名', 'お問い合わせの種類', 'お問い合わせ内容', '登録日時']);

            // 性別の数値をテキストに変換するラベル
            $genderLabels = [1 => '男性', 2 => '女性', 3 => 'その他'];

            // 各データを1行ずつ書き込む
            foreach ($contacts as $contact) {
                // 初期データと追加データの「苗字 名前」のズレを吸収して統一
                $fullname = $contact->id <= 20
                    ? $contact->last_name.' '.$contact->first_name
                    : $contact->first_name.' '.$contact->last_name;

                fputcsv($stream, [
                    $contact->id,
                    $fullname,
                    $genderLabels[$contact->gender] ?? '不明',
                    $contact->email,
                    $contact->tel,
                    $contact->address,
                    $contact->building ?? '',
                    $contact->category->content ?? '',
                    $contact->detail,
                    $contact->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($stream);
        });

        // 4. ダウンロードさせるためのファイル名などの設定（ヘッダー）
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="contacts_export_'.date('YmdHis').'.csv"');

        return $response;
    }
}
