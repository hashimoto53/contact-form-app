<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ExportController;

/*
|--------------------------------------------------------------------------
| 一般ユーザー用（お問い合わせフォーム）
|--------------------------------------------------------------------------
*/
// 1. お問い合わせ入力画面 (PG01)
Route::get('/', [ContactController::class, 'index'])->name('contact.index');

// 2. お問い合わせ確認画面 (PG02)
Route::post('/contacts/confirm', [ContactController::class, 'confirm'])->name('contact.confirm');

// 3. お問い合わせ送信処理（DB保存）
Route::post('/contacts', [ContactController::class, 'store'])->name('contact.store');

// 4. サンクスページ（送信完了画面）(PG03)
Route::get('/thanks', [ContactController::class, 'thanks'])->name('contact.thanks');


/*
|--------------------------------------------------------------------------
| 管理者用（管理画面：ログイン必須の制限）
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // 管理画面の一覧・検索 (PG05)
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');

    // お問い合わせ詳細ページ (PG05-2) ※仕様書通りページ遷移のGETルート
    Route::get('/admin/contacts/{contact}', [AdminController::class, 'show'])->name('admin.show');

    // お問い合わせ削除 ※詳細ページからのDELETEリクエスト用
    Route::delete('/admin/contacts/{contact}', [AdminController::class, 'destroy'])->name('admin.destroy');

    // エクスポート（CSVダウンロード：応用機能）
    Route::get('/contacts/export', [ExportController::class, 'export'])->name('contacts.export');
});

/*
|--------------------------------------------------------------------------
| 【重要】Fortify認証ルートの有効化（仕様書準拠）
|--------------------------------------------------------------------------
| この1行を追加することで、Fortifyの内部ログイン処理やバリデーションへの
| 正しい通り道（loginルート）がシステムに完全合流します。
*/
require __DIR__.'/auth.php';