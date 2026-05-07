@extends('skin.layout.basic.main')

@section('title', ' - 이메일 인증')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-xl shadow-lg p-8 text-center">
        <div style="width:64px;height:64px;border-radius:50%;background:#dbeafe;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
            <svg width="32" height="32" fill="none" stroke="#2271b1" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
            </svg>
        </div>

        <h1 class="text-2xl font-bold text-gray-800 mb-3">이메일 인증이 필요합니다</h1>
        <p class="text-gray-500 mb-6">
            <strong>{{ auth()->user()->email }}</strong>로 인증 메일을 발송했습니다.<br>
            메일함을 확인하고 인증 링크를 클릭해주세요.
        </p>

        @if(session('status') === 'verification-link-sent')
            <div style="padding:10px 14px;margin-bottom:16px;background:#dcfce7;border-radius:6px;color:#166534;font-size:14px;">
                인증 메일이 재발송되었습니다.
            </div>
        @endif

        @if(session('success'))
            <div style="padding:10px 14px;margin-bottom:16px;background:#dbeafe;border-radius:6px;color:#1e40af;font-size:14px;">
                {{ session('success') }}
            </div>
        @endif

        <div style="display:flex;flex-direction:column;gap:10px;">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="w-full bg-blue-600 text-white py-2.5 rounded-lg font-medium hover:bg-blue-700 transition-colors">
                    인증 메일 재발송
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full bg-gray-100 text-gray-600 py-2.5 rounded-lg font-medium hover:bg-gray-200 transition-colors">
                    로그아웃
                </button>
            </form>
        </div>

        <p class="text-xs text-gray-400 mt-6">
            메일이 오지 않는 경우 스팸함을 확인하거나 재발송 버튼을 눌러주세요.
        </p>
    </div>
</div>
@endsection
