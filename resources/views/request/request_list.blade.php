@extends('layouts.app')
@section('title', '申請一覧')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/request/request_list.css') }}">
@endpush

@section('content')
<main>
    <div class="request">
        <div class="request-list">
            <h1 class="request-list__title">申請一覧</h1>

            <div class="tabs">
                <a href="{{ route('requests.index', ['page' => 'wait']) }}" class="tab {{ request('page', 'wait') === 'wait' ? 'tab--active' : '' }}">承認待ち</a>
                <a href="{{ route('requests.index', ['page' => 'done']) }}" class="tab {{ request('page') === 'done' ? 'tab--active' : '' }}">承認済み</a>
            </div>

            <table>
                <thead class="request-list__header-row">
                    <tr>
                        <th class="table-situation">状態</th>
                        <th class="table-name">名前</th>
                        <th class="table-work-date">対象日時</th>
                        <th class="table-request-reason">申請理由</th>
                        <th class="table-request-at">申請日時</th>
                        <th class="table-detail">詳細</th>
                    </tr>
                </thead>

                <tbody class="request-list__body-row">
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
                            </td> --}}

                            <td class="table-detail">
                                {{--@if ($attendanceForDate)
                                    <a class="button-detail" href="{{ route('attendance.detail', ['attendance' => $attendanceForDate->id]) }}">詳細</a>
                                @else--}}
                                    <button class="button-detail">詳細</button>
                                {{--@endif---}}
                            </td>
                        </tr>
                    {{--@endforeach--}}
                </tbody>
            </table>
        </div>
    </div>
</main>
@endsection
