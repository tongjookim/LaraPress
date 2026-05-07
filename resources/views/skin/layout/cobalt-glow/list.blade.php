@extends('skin.layout.swn-style.main')

@section('title', " - {$board->board_name}")

@push('skin-css')
    @vite(['resources/views/skin/board/' . $board->skin . '/style.css'])
@endpush

@section('content')
    @yield('board-content')
@endsection
