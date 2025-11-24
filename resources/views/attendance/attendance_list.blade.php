@extends('layouts.app')
@section('title', '勤怠一覧')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/attendance/attendance_list.css') }}">
@endpush

@section('content')
<main>
    <div class="attendance-list">
        <h1 class="attendance-list__title">勤怠一覧</h1>
        <div class="attendance-list__header">
            <div class="previous-month">
                <a href="{{ route('attendance.index', ['month'=> $previousMonth]) }}">←前月</a>
            </div>
            <div class="this-month">{{ $startOfMonth->format('Y年m月') }}</div>
            <div class="next-month">
                <a href="{{ route('attendance.index', ['month'=> $nextMonth]) }}">翌月→</a>
            </div>
        </div>

        <table>
            <thead class="attendance-list__head">
                <tr>
                    <th class="table-days">日付</th>
                    <th class="table-clock_in">出勤</th>
                    <th class="table-clock_out">退勤</th>
                    <th class="table-break">休憩</th>
                    <th class="table-total">合計</th>
                    <th class="table-detail">詳細</th>
                </tr>
            </thead>

            <tbody class="attendance-list__body">
                @foreach ($dates as $date)
                    <tr>
                        <td class="table-days">{{ \Carbon\Carbon::parse($date)->locale('ja')->isoFormat('MM/DD(ddd)') }}</td>
                        <td class="table-clock-in">{{ $dates->$attendance->clock_in_at }}</td>
                        <td class="table-clock-out">{{ $attendance->clock_out_at }}</td>
                        <td class="table-break"></td>
                        <td class="table-total"></td>
                        <td class="table-detail">
                            <button class="button-detail">詳細</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</main>
@endsection
