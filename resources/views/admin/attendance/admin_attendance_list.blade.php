@extends('layouts.app')
@section('title', '管理者勤怠一覧')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/attendance/admin_attendance_list.css') }}">
@endpush

@section('content')
<main>
    <div class="attendance">
        <div class="attendance-list">
            <h1 class="attendance-list__title">{{ $targetDay->format('Y年n月j日') }}の勤怠</h1>
            <div class="attendance-list__header">
                <a class="previous-day" href="{{ route('admin.attendance.index', ['date'=> $previousDay]) }}">
                    <p class="arrow">←</p>
                    <span>前日</span>
                </a>
                <div class="this-day">
                    <img class="this-day__icon" src="{{ asset('images/calendar.svg') }}" alt="カレンダーアイコン">
                    <span class="this-day__text">{{ $targetDay->format('Y/m/d') }}</span>
                </div>
                <a class="next-day" href="{{ route('admin.attendance.index', ['date'=> $nextDay]) }}">
                    <span>翌日</span>
                    <p class="arrow">→</p>
                </a>
            </div>

            <table>
                <thead class="attendance-list__header-row">
                    <tr>
                        <th class="table-name">名前</th>
                        <th class="table-clock-in">出勤</th>
                        <th class="table-clock-out">退勤</th>
                        <th class="table-break">休憩</th>
                        <th class="table-total">合計</th>
                        <th class="table-detail">詳細</th>
                    </tr>
                </thead>

                <tbody class="attendance-list__body-row">
                    @foreach ($attendances as $attendance)
                        <tr>
                            <td class="table-name">{{ $attendance->user->name }}</td>
                            <td class="table-clock-in">
                                {{ ($attendance->clock_in_at)
                                    ? $attendance->clock_in_at->format('H:i') : '' }}
                            </td>

                            <td class="table-clock-out">
                                {{ ($attendance->clock_out_at)
                                    ? $attendance->clock_out_at->format('H:i') : '' }}
                            </td>

                            <td class="table-break">
                                {{ ($attendance->the_total_break)
                                    ? $attendance->the_total_break : '' }}
                            </td>

                            <td class="table-total">
                                {{ ($attendance->the_total_work)
                                    ? $attendance->the_total_work : '' }}
                            </td>

                            <td class="table-detail">
                                <a class="button-detail" href="{{ route('admin.attendance.detail', ['attendance' => $attendance->id]) }}">詳細</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</main>
@endsection
