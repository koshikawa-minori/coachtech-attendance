@extends('layouts.app')
@section('title', '勤怠一覧')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/staff/staff_attendance.css') }}">
@endpush

@section('content')
<main>
    <div class="staff">
        <div class="staff-list">
            <h1 class="staff-list__title">○○さんの勤怠</h1>
            <div class="staff-list__header">
                <a class="previous-month" href="{{-- route('attendance.index', ['month'=> $previousMonth]) --}}">
                    <p class="arrow">←</p>
                    <span>前月</span>
                </a>
                <div class="this-month">
                    <img src="{{-- asset('images/calendar.svg') }}" class="this-month__icon" alt="カレンダーアイコン">
                    <span class="this-month__text">{{ $startOfMonth->format('Y/m') --}}</span>
                </div>
                <a class="next-month" href="{{--route('attendance.index', ['month'=> $nextMonth]) --}}">
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
                    {{--@foreach ($dates as $date)
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
                    @endforeach--}}
                </tbody>
            </table>
        </div>
    </div>
</main>
@endsection
