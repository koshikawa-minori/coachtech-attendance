@extends('layouts.app')
@section('title', '勤怠一覧')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/attendance/attendance_list.css') }}">
@endpush

@section('content')
<main>
    <div class="attendance">
        <div class="attendance-list">
            <h1 class="attendance-list__title">勤怠一覧</h1>
            <div class="attendance-list__header">
                <div class="previous-month">
                    <p class="arrow">←</p>
                    <a href="{{ route('attendance.index', ['month'=> $previousMonth]) }}">前月</a>
                </div>
                <div class="this-month">
                    <img src="{{ asset('images/calendar.svg') }}" class="this-month__icon" alt="カレンダーアイコン">
                    <span class="this-month__text">{{ $startOfMonth->format('Y/m') }}</span>
                </div>
                <div class="next-month">
                    <a href="{{ route('attendance.index', ['month'=> $nextMonth]) }}">翌月</a>
                    <p class="arrow">→</p>
                </div>
            </div>

            <table>
                <thead class="attendance-list__header-row">
                    <tr>
                        <th class="table-days">日付</th>
                        <th class="table-clock-in">出勤</th>
                        <th class="table-clock-out">退勤</th>
                        <th class="table-break">休憩</th>
                        <th class="table-total">合計</th>
                        <th class="table-detail">詳細</th>
                    </tr>
                </thead>

                <tbody class="attendance-list__body-row">
                    @foreach ($dates as $date)
                        @php
                            $dateCarbon = \Carbon\Carbon::parse($date);
                            $attendanceForDate = $attendances->firstWhere('work_date', $dateCarbon);
                            $formattedDate = $dateCarbon->isoFormat('MM/DD(ddd)');
                        @endphp
                        <tr>
                            <td class="table-days">{{ $formattedDate }}</td>
                            <td class="table-clock-in">
                                {{ ($attendanceForDate && $attendanceForDate->clock_in_at)
                                    ? $attendanceForDate->clock_in_at->format('H:i') : '' }}
                            </td>

                            <td class="table-clock-out">
                                {{ ($attendanceForDate && $attendanceForDate->clock_out_at)
                                    ? $attendanceForDate->clock_out_at->format('H:i') : '' }}
                            </td>

                            <td class="table-break-total">
                                {{ ($attendanceForDate && $attendanceForDate->the_total_break)
                                    ? $attendanceForDate->the_total_break : '' }}
                            </td>

                            <td class="table-attendance-total">
                                {{ ($attendanceForDate && $attendanceForDate->the_total_work)
                                    ? $attendanceForDate->the_total_work : '' }}
                            </td>

                            <td class="table-detail">
                                @if ($attendanceForDate)
                                    <a class="button-detail" href="{{ route('attendance.detail', ['attendance' => $attendanceForDate->id]) }}">詳細</a>
                                @else
                                    <button class="button-detail">詳細</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</main>
@endsection
