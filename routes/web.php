<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

//
Route::get('/', function () {
    return view('welcome');
});

// メール認証誘導画面
Route::view('/register/verify', 'auth.register-verify')->middleware('auth')->name('register.verify');

// メール認証画面
Route::get('/email/verify', function ()
{
    return view('auth.verify');
})->middleware('auth')->name('verification.notice');

// メール認証画面から勤務登録画面へ
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request)
{
    $request->fulfill();
    return redirect()->route('attendance.create');

})->middleware(['auth', 'signed', 'throttle:6,1'])->name('verification.verify');

// メール認証再送
Route::post('/email/verification-notification', function (Request $request)
{
    if ($request->user()->hasVerifiedEmail()) {
        return back();
    }
    $request->user()->sendEmailVerificationNotification();
    return back();

})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// 認証必須ページ
//Route::middleware(['auth'])->group(function()
//{
    // ログアウト処理（一般ユーザー）
    //出勤登録画面（一般ユーザー）
    // 仮の勤務登録画面（遷移確認用）
Route::get('/attendance', function () {
    return view('attendance.attendance');
})->middleware('auth')->name('attendance.create');

    // 出勤登録処理（一般ユーザー）
    // 休憩入処理（一般ユーザー）
    // 休憩戻処理（一般ユーザー）
    // 退勤処理（一般ユーザー）
    // 勤怠一覧画面（一般ユーザー）
    // 勤怠詳細画面（一般ユーザー）
    // 勤怠詳細画面＿修正申請（一般ユーザー）
    // 申請一覧画面（一般ユーザー）

    //admin
    // ログアウト処理（管理者）
    // 勤怠一覧画面（管理者）
    // 勤怠詳細画面（管理者）
    // スタッフ一覧画面（管理者）
    // スタッフ別勤怠一覧画面（管理者）
    // 申請一覧画面（管理者）
    // 修正申請承認画面（管理者）
    // 修正申請承認処理（管理者）
//});