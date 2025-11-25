@extends('layouts.app')
@section('title', '勤怠詳細')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/attendance/attendance_detail.css') }}">
@endpush

@section('content')
<main>
    <div class="attendance">
        <div class="attendance-detail">
            <h1 class="attendance-detail__title">勤怠詳細</h1>
            <table>
                <thead class="attendance-list__head">
                    <tr>
                        <th class="table-name">名前</th>
                        <th class="table-days">日付</th>
                        <th class="table-clock_inOut">出勤・退勤</th>
                        <th class="table-break">休憩</th>
                        <th class="table-break2">休憩２</th>
                        <th class="table-note">備考</th>
                    </tr>
                </thead>

                <tbody class="attendance-list__body">
                    <tr>
                        <th class="table-name"></th>
                        <th class="table-days"></th>
                        <th class="table-clock_inOut"></th>
                        <th class="table-break"></th>
                        <th class="table-break2"></th>
                        <th class="table-note"></th>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</main>
@endsection
