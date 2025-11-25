<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\UserRequestController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\AdminRequestController;

// メール認証誘導画面
Route::view('/register/verify', 'auth.register_verify', ['headerType' => 'auth'])
->middleware('auth')->name('register.verify');

// メール認証画面
Route::get('/email/verify', function ()
{
    return view('auth.verify', ['headerType' => 'auth']);
})->middleware('auth')->name('verification.notice');

// メール認証画面から勤務登録画面へ
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request)
{
    $request->fulfill();
    return redirect()->route('attendance.show');

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
Route::middleware(['auth'])->group(function()
{
    //勤怠登録画面（一般ユーザー）
    Route::get('/attendance', [AttendanceController::class, 'show'])->name('attendance.show');

    // 出勤・休憩・退勤処理（一般ユーザー）
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');

    // 勤怠一覧画面（一般ユーザー）
    Route::get('/attendance/list', [AttendanceController::class, 'index'])->name('attendance.index');

    // 勤怠詳細画面（一般ユーザー）
    Route::get('/attendance/detail/{attendance}', [AttendanceController::class, 'detail'])->name('attendance.detail');
    // 勤怠詳細画面＿修正申請（一般ユーザー）
    // 申請一覧画面（一般ユーザー）
    Route::get('/requests', function ()
    {
        return view('request.request_list');
    })->name('requests.index');


    //admin
    // ログアウト処理（管理者）
    // 勤怠一覧画面（管理者）
    // 勤怠詳細画面（管理者）
    // スタッフ一覧画面（管理者）
    // スタッフ別勤怠一覧画面（管理者）
    // 申請一覧画面（管理者）
    // 修正申請承認画面（管理者）
    // 修正申請承認処理（管理者）
});