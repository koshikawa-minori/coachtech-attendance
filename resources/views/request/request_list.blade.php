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
                <a href="{{ route('requests.index', ['page' => 'wait']) }}" class="tab {{ $page === 'wait' ? 'tab--active' : '' }}">承認待ち</a>
                <a href="{{ route('requests.index', ['page' => 'done']) }}" class="tab {{ $page === 'done' ? 'tab--active' : '' }}">承認済み</a>
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
                    @foreach ($attendanceCorrections as $attendanceCorrection)
                        <tr>
                            <td class="table-situation">
                                {{ $attendanceCorrection->status ? '承認済み' : '承認待ち' }}
                            </td>

                            <td class="table-name">
                                {{ optional(optional($attendanceCorrection->attendance)->user)->name }}
                            </td>

                            <td class="table-work-date">
                                {{ optional(optional($attendanceCorrection->attendance)->work_date)->format('Y/m/d')}}
                            </td>

                            <td class="table-request-reason">
                                {{ $attendanceCorrection->requested_notes}}
                            </td>

                            <td class="table-request-at">
                                {{ $attendanceCorrection->created_at->format('Y/m/d')}}
                            </td>

                            <td class="table-detail">
                                {{--@if ($attendanceForDate)
                                    <a class="button-detail" href="{{ route('requests.index', ['attendance' => $attendanceForDate->id]) }}">詳細</a>
                                @else}--}}
                                    <button class="button-detail">詳細</button>
                                {{--@endif--}}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</main>
@endsection
