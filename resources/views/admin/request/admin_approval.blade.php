@extends('layouts.app')
@section('title', '修正申請承認')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/request/admin_approval.css') }}">
@endpush

@section('content')
<div class="request">
    <div class="request-list">
        <h1 class="request-list__title">勤怠詳細</h1>

        <div class="detail__card">
            <div class="detail__group">
                <div class="detail__title">名前</div>
                <div class="detail__display">
                    {{ $attendanceCorrection->attendance->user->name }}
                </div>
            </div>

            <div class="detail__group">
                <div class="detail__title">日付</div>
                <div class="detail__date">
                    <div class="detail__year">
                        {{ $attendanceCorrection->attendance->work_date->format('Y年') }}
                    </div>

                    <div class="detail__days">
                        {{ $attendanceCorrection->attendance->work_date->format('m月d日') }}
                    </div>
                </div>
            </div>

            <div class="detail__group">
                <div class="detail__title">出勤・退勤</div>
                <div class="detail__content">
                    {{ $attendanceCorrection->requested_clock_in_at->format('H:i') }}
                </div>
                <span>～</span>
                <div class="detail__content">
                    {{ $attendanceCorrection->requested_clock_out_at->format('H:i') }}
                </div>
            </div>

            <div class="detail__group">
                <div class="detail__title">休憩</div>
                <div class="detail__content">
                    {{ $attendanceCorrection->requested_breaks[0]['start'] ?? '' }}
                </div>
                <span>～</span>
                <div class="detail__content">
                    {{ $attendanceCorrection->requested_breaks[0]['end'] ?? '' }}
                </div>
            </div>

            <div class="detail__group">
                <div class="detail__title">休憩２</div>
                <div class="detail__content">
                    {{ $attendanceCorrection->requested_breaks[1]['start'] ?? '' }}
                </div>
                <span>～</span>
                <div class="detail__content">
                    {{ $attendanceCorrection->requested_breaks[1]['end'] ?? '' }}
                </div>
            </div>

            <div class="detail__group">
                <div class="detail__title">備考</div>
                <div class="detail__content">
                    {{ $attendanceCorrection->requested_notes }}
                </div>
            </div>
        </div>

        <button class="detail__button" type="submit">承認</button>
    </div>
</div>
@endsection
