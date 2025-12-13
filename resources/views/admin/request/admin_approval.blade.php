@extends('layouts.app')
@section('title', '申請一覧')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/request/request_list.css') }}">
@endpush

@section('content')
<div class="request">
    <div class="request-list">
        <h1 class="request-list__title">申請一覧</h1>