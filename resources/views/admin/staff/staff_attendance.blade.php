@extends('layouts.app')
@section('title', 'スタッフ別勤怠一覧')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/staff/staff_attendance.css') }}">
@endpush

@section('content')
<div class="staff">
    <div class="staff-list">
        <h1 class="staff-list__title">{{ $staffUser->name }}さんの勤怠</h1>
        <div class="staff-list__header">
            <a class="previous-month" href="{{ route('admin.staff.attendance', ['staffId' => $staffUser->id, 'month'=> $previousMonth])}}">
                <p class="arrow">←</p>
                <span>前月</span>
            </a>
            <div class="this-month">
                <img src="{{ asset('images/calendar.svg') }}" class="this-month__icon" alt="カレンダーアイコン">
                <span class="this-month__text">{{ $startOfMonth->format('Y/m') }}</span>
            </div>
            <a class="next-month" href="{{ route('admin.staff.attendance', ['staffId' => $staffUser->id, 'month'=> $nextMonth])}}">
                <span>翌月</span>
                <p class="arrow">→</p>
            </a>
        </div>

        <table>
            <thead class="staff-list__header-row">
                <tr>
                    <th class="table-days">日付</th>
                    <th class="table-clock-in">出勤</th>
                    <th class="table-clock-out">退勤</th>
                    <th class="table-break">休憩</th>
                    <th class="table-total">合計</th>
                    <th class="table-detail">詳細</th>
                </tr>
            </thead>

            <tbody class="staff-list__body-row">
                @foreach ($rows as $row)
                    <tr>
                        <td class="table-days">{{ $row['formatted_date'] }}</td>

                        <td class="table-clock-in">{{ $row['clock_in'] ?? '' }}</td>

                        <td class="table-clock-out">{{ $row['clock_out'] ?? '' }}</td>

                        <td class="table-break-total">{{ $row['break_total'] ?? '' }}</td>

                        <td class="table-attendance-total">{{ $row['work_total'] ?? '' }}</td>

                        <td class="table-detail">
                            @if ($row['attendance_id'])
                                <a class="button-detail" href="{{ route('admin.attendance.detail', ['attendance' => $row['attendance_id']]) }}">詳細</a>
                            @else
                                <button class="button-detail" type="button">詳細</button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <a class="button-csv" href="{{ route('admin.staff.attendance.export', ['staffId' => $staffUser->id, 'month' => $startOfMonth->format('Y-m')]) }}" >CSV出力</a>
    </div>
</div>
@endsection
