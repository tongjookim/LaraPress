@php
    $__skin = 'newyorktimes-style';
@endphp

@include('skin.layout.newyorktimes-style.head')
@include('skin.layout.newyorktimes-style.navigation')

<main style="background:#fff;min-height:60vh;">
    @yield('content')
</main>

@include('skin.layout.newyorktimes-style.footer')
@stack('scripts')
