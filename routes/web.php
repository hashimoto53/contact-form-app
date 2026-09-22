<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 一般ユーザー用（お問い合わせフォーム）
|--------------------------------------------------------------------------
*/
Route::get('/', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contacts/confirm', [ContactController::class, 'confirm'])->name('contact.confirm');
Route::post('/contacts', [ContactController::class, 'store'])->name('contact.store');
Route::get('/thanks', [ContactController::class, 'thanks'])->name('contact.thanks');

/*
|--------------------------------------------------------------------------
| 管理者用（管理画面：ログイン必須）
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // 管理画面の一覧・検索 (PG05)
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');

    // お問い合わせ詳細ページ (PG05-2) ※仕様書準拠のページ遷移（GET）
    Route::get('/admin/contacts/{contact}', [AdminController::class, 'show'])->name('admin.show');

    // お問い合わせ削除
    Route::delete('/admin/contacts/{contact}', [AdminController::class, 'destroy'])->name('admin.destroy');

    // タグ管理マスタのCRUD機能（実装漏れの解消）
    Route::resource('/admin/tags', TagController::class);

    // エクスポート（CSVダウンロード）
    Route::get('/contacts/export', [ExportController::class, 'export'])->name('contacts.export');
});
