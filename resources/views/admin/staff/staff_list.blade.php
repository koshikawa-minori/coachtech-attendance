@extends('layouts.app')
@section('title', 'スタッフ一覧')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/staff/staff_list.css') }}">
@endpush

@section('content')
<main>
    <div class="staff">
        <div class="staff-list">
            <h1 class="staff-list__title">スタッフ一覧</h1>

            <table>
                <thead class="staff-list__header-row">
                    <tr>
                        <th class="table-name">名前</th>
                        <th class="table-email">メールアドレス</th>
                        <th class="table-month-attendance">月次勤怠</th>
                    </tr>
                </thead>

                <tbody class="staff-list__body-row">
                    <tr>
                        <td class="table-name"></td>
                        <td class="table-email"></td>
                        <td class="table-month-attendance"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</main>
@endsection
