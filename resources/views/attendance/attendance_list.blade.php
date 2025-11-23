@extends('layouts.app')
@section('title', '勤怠一覧')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/attendance/attendance_list.css') }}">
@endpush

@section('content')
<main>
    <h1 class="list__title">勤怠一覧</h1>
    <div class="list__header">
        {{-- ページネーションと今月表示 --}}
    </div>

    <table>
        {{-- 月の出勤一覧--}}
    </table>