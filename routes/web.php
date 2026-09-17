<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| 一般ユーザー用（お問い合わせフォーム）
|--------------------------------------------------------------------------
*/
// 1. お問い合わせ入力画面
Route::get('/', [ContactController::class, 'index'])->name('contact.index');

// 2. お問い合わせ確認画面
Route::post('/contacts/confirm', [ContactController::class, 'confirm'])->name('contact.confirm');

// 3. お問い合わせ送信処理（DB保存）
Route::post('/contacts', [ContactController::class, 'store'])->name('contact.store');

// 4. サンクスページ（送信完了画面）
Route::get('/thanks', [ContactController::class, 'thanks'])->name('contact.thanks');


/*
|--------------------------------------------------------------------------
| 管理者用（管理画面）
|--------------------------------------------------------------------------
*/
// Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
// });