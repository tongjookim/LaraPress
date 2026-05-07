@extends('admin.layout')
@section('title', '테마 설정')

@section('admin-content')
<h1 class="wp-page-title">테마 설정</h1>

@if(session('success'))
    <div class="wp-notice" style="margin-bottom:16px;">{{ session('success') }}</div>
@endif

{{-- 탭 --}}
<div style="display:flex;gap:0;border-bottom:2px solid #c3c4c7;margin-bottom:24px;">
    <button type="button" class="theme-tab active" data-target="tab-menu"
            style="padding:8px 20px;font-size:13px;font-weight:600;background:none;border:none;border-bottom:2px solid #2271b1;margin-bottom:-2px;cursor:pointer;color:#2271b1;">
        메뉴 관리
    </button>
    <button type="button" class="theme-tab" data-target="tab-footer"
            style="padding:8px 20px;font-size:13px;font-weight:600;background:none;border:none;border-bottom:2px solid transparent;margin-bottom:-2px;cursor:pointer;color:#646970;">
        푸터 메뉴
    </button>
    <button type="button" class="theme-tab" data-target="tab-widget"
            style="padding:8px 20px;font-size:13px;font-weight:600;background:none;border:none;border-bottom:2px solid transparent;margin-bottom:-2px;cursor:pointer;color:#646970;">
        위젯 설정
    </button>
    <button type="button" class="theme-tab" data-target="tab-color"
            style="padding:8px 20px;font-size:13px;font-weight:600;background:none;border:none;border-bottom:2px solid transparent;margin-bottom:-2px;cursor:pointer;color:#646970;">
        색상 설정
    </button>
</div>

{{-- ══ 메뉴 관리 탭 ══════════════════════════════════════════ --}}
<div id="tab-menu">
    <div style="display:grid;grid-template-columns:1fr 320px;gap:20px;align-items:start;">

        {{-- 현재 메뉴 목록 --}}
        <div class="wp-widget">
            <div class="wp-widget-header" style="display:flex;align-items:center;justify-content:space-between;">
                <span>메뉴 항목</span>
                <span style="font-size:12px;font-weight:400;color:#646970;">드래그하여 순서 변경</span>
            </div>
            <div class="wp-widget-body" style="padding:0;">
                @if($menus->isEmpty())
                    <p style="padding:20px;text-align:center;color:#8c8f94;font-size:13px;">
                        메뉴 항목이 없습니다. 오른쪽에서 추가하세요.
                    </p>
                @else
                <ul id="menu-sortable" style="list-style:none;margin:0;padding:0;">
                    @foreach($menus as $menu)
                    <li data-id="{{ $menu->id }}"
                        style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-bottom:1px solid #f0f0f1;cursor:grab;background:#fff;"
                        class="menu-row">
                        <span style="color:#c3c4c7;cursor:grab;flex-shrink:0;">
                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M8 6h8M8 12h8M8 18h8" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" fill="none"/></svg>
                        </span>
                        <div style="flex:1;min-width:0;">
                            <span style="font-size:13px;font-weight:600;color:{{ $menu->is_active ? '#1d2327' : '#8c8f94' }};">
                                {{ $menu->label }}
                            </span>
                            <span style="font-size:12px;color:#8c8f94;margin-left:8px;">{{ $menu->url }}</span>
                            @if(!$menu->is_active)
                                <span style="font-size:11px;color:#d63638;margin-left:6px;">(비활성)</span>
                            @endif
                            @if($menu->target === '_blank')
                                <span style="font-size:11px;color:#646970;margin-left:6px;">↗ 새창</span>
                            @endif
                        </div>
                        <div style="display:flex;gap:4px;flex-shrink:0;">
                            <button type="button"
                                    onclick="openEditModal({{ $menu->id }}, '{{ addslashes($menu->label) }}', '{{ addslashes($menu->url) }}', '{{ $menu->target }}', {{ $menu->is_active ? 'true' : 'false' }})"
                                    class="wp-btn wp-btn-secondary wp-btn-sm">수정</button>
                            <form method="POST" action="{{ route('admin.theme.menu.delete', $menu->id) }}" onsubmit="return confirm('삭제하시겠습니까?');" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="wp-btn wp-btn-danger wp-btn-sm">삭제</button>
                            </form>
                        </div>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>

        {{-- 메뉴 추가 폼 --}}
        <div>
            <div class="wp-widget">
                <div class="wp-widget-header">메뉴 항목 추가</div>
                <div class="wp-widget-body">
                    <form method="POST" action="{{ route('admin.theme.menu.store') }}">
                        @csrf
                        <div class="wp-form-group">
                            <label class="wp-form-label">메뉴명 *</label>
                            <input type="text" name="label" class="wp-form-input" required maxlength="60"
                                   placeholder="예: 홈, 뉴스, 자유게시판">
                        </div>
                        <div class="wp-form-group">
                            <label class="wp-form-label">URL *</label>
                            <input type="text" name="url" class="wp-form-input" required maxlength="300"
                                   placeholder="예: /, /news, /bbs/free">
                        </div>
                        <div class="wp-form-group">
                            <label class="wp-form-label">열기 방식</label>
                            <select name="target" class="wp-form-input wp-form-select">
                                <option value="_self">현재 창</option>
                                <option value="_blank">새 창</option>
                            </select>
                        </div>
                        <button type="submit" class="wp-btn wp-btn-primary" style="width:100%;">추가</button>
                    </form>
                </div>
            </div>

            <div class="wp-widget" style="margin-top:12px;">
                <div class="wp-widget-header">빠른 추가</div>
                <div class="wp-widget-body">

                    {{-- 뉴스 전체 --}}
                    <p style="font-size:11px;font-weight:700;color:#1d2327;margin-bottom:6px;text-transform:uppercase;letter-spacing:.04em;">뉴스</p>
                    <form method="POST" action="{{ route('admin.theme.menu.store') }}" style="display:inline;">
                        @csrf
                        <input type="hidden" name="label" value="뉴스 전체">
                        <input type="hidden" name="url" value="/news">
                        <input type="hidden" name="target" value="_self">
                        <button type="submit" class="wp-btn wp-btn-secondary wp-btn-sm" style="margin-bottom:6px;">+ 뉴스 전체</button>
                    </form>

                    {{-- 기사 카테고리 --}}
                    @php $articleCategories = App\Models\ArticleCategory::where('is_active', true)->orderBy('order')->get(); @endphp
                    @if($articleCategories->isNotEmpty())
                    <div style="margin-top:10px;margin-bottom:6px;">
                        <p style="font-size:11px;font-weight:700;color:#1d2327;margin-bottom:6px;text-transform:uppercase;letter-spacing:.04em;">기사 카테고리</p>
                        @foreach($articleCategories as $cat)
                        <form method="POST" action="{{ route('admin.theme.menu.store') }}" style="display:inline;">
                            @csrf
                            <input type="hidden" name="label" value="{{ $cat->name }}">
                            <input type="hidden" name="url" value="/news?category={{ $cat->slug }}">
                            <input type="hidden" name="target" value="_self">
                            <button type="submit" class="wp-btn wp-btn-secondary wp-btn-sm" style="margin-bottom:4px;">+ {{ $cat->name }}</button>
                        </form>
                        @endforeach
                    </div>
                    @endif

                    {{-- 게시판 --}}
                    @php $boards = App\Models\Board::where('is_active', true)->orderBy('order')->get(); @endphp
                    @if($boards->isNotEmpty())
                    <div style="margin-top:10px;">
                        <p style="font-size:11px;font-weight:700;color:#1d2327;margin-bottom:6px;text-transform:uppercase;letter-spacing:.04em;">게시판</p>
                        @foreach($boards as $b)
                        <form method="POST" action="{{ route('admin.theme.menu.store') }}" style="display:inline;">
                            @csrf
                            <input type="hidden" name="label" value="{{ $b->board_name }}">
                            <input type="hidden" name="url" value="/bbs/{{ $b->board_id }}">
                            <input type="hidden" name="target" value="_self">
                            <button type="submit" class="wp-btn wp-btn-secondary wp-btn-sm" style="margin-bottom:4px;">+ {{ $b->board_name }}</button>
                        </form>
                        @endforeach
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══ 푸터 메뉴 탭 ════════════════════════════════════════════ --}}
<div id="tab-footer" style="display:none;">
    <div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start;">

        {{-- 현재 푸터 메뉴 목록 --}}
        <div class="wp-widget">
            <div class="wp-widget-header" style="display:flex;align-items:center;justify-content:space-between;">
                <span>푸터 메뉴 항목</span>
                <span style="font-size:12px;font-weight:400;color:#646970;">그룹별로 분류됩니다</span>
            </div>
            <div class="wp-widget-body" style="padding:0;">
                @if($footerMenus->isEmpty())
                    <p style="padding:20px;text-align:center;color:#8c8f94;font-size:13px;">
                        푸터 메뉴 항목이 없습니다. 오른쪽에서 추가하세요.
                    </p>
                @else
                @php $currentGroup = null; @endphp
                @foreach($footerMenus as $fmenu)
                    @if($fmenu->group !== $currentGroup)
                        @if($currentGroup !== null)</ul>@endif
                        @php $currentGroup = $fmenu->group; @endphp
                        <div style="padding:8px 14px 4px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#646970;background:#f9f9f9;border-bottom:1px solid #f0f0f1;">
                            {{ $currentGroup ?: '기타' }}
                        </div>
                        <ul style="list-style:none;margin:0;padding:0;">
                    @endif
                    <li style="display:flex;align-items:center;gap:10px;padding:9px 14px;border-bottom:1px solid #f0f0f1;background:#fff;">
                        <div style="flex:1;min-width:0;">
                            <span style="font-size:13px;font-weight:600;color:{{ $fmenu->is_active ? '#1d2327' : '#8c8f94' }};">
                                {{ $fmenu->label }}
                            </span>
                            <span style="font-size:12px;color:#8c8f94;margin-left:8px;">{{ $fmenu->url }}</span>
                            @if(!$fmenu->is_active)
                                <span style="font-size:11px;color:#d63638;margin-left:6px;">(비활성)</span>
                            @endif
                            @if($fmenu->target === '_blank')
                                <span style="font-size:11px;color:#646970;margin-left:6px;">↗ 새창</span>
                            @endif
                        </div>
                        <div style="display:flex;gap:4px;flex-shrink:0;">
                            <button type="button"
                                    onclick="openFooterEditModal({{ $fmenu->id }}, '{{ addslashes($fmenu->label) }}', '{{ addslashes($fmenu->url) }}', '{{ addslashes($fmenu->group ?? '') }}', '{{ $fmenu->target }}', {{ $fmenu->is_active ? 'true' : 'false' }})"
                                    class="wp-btn wp-btn-secondary wp-btn-sm">수정</button>
                            <form method="POST" action="{{ route('admin.theme.footer-menu.delete', $fmenu->id) }}" onsubmit="return confirm('삭제하시겠습니까?');" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="wp-btn wp-btn-danger wp-btn-sm">삭제</button>
                            </form>
                        </div>
                    </li>
                @endforeach
                @if($currentGroup !== null)</ul>@endif
                @endif
            </div>
        </div>

        {{-- 푸터 메뉴 추가 폼 --}}
        <div>
            <div class="wp-widget">
                <div class="wp-widget-header">푸터 메뉴 항목 추가</div>
                <div class="wp-widget-body">
                    <form method="POST" action="{{ route('admin.theme.footer-menu.store') }}">
                        @csrf
                        <div class="wp-form-group">
                            <label class="wp-form-label">그룹명 *</label>
                            <input type="text" name="group" class="wp-form-input" required maxlength="60"
                                   placeholder="예: 회사소개, 서비스, 고객지원">
                            <p style="font-size:11px;color:#8c8f94;margin-top:3px;">같은 그룹명 항목끼리 한 열에 표시됩니다.</p>
                        </div>
                        <div class="wp-form-group">
                            <label class="wp-form-label">메뉴명 *</label>
                            <input type="text" name="label" class="wp-form-input" required maxlength="60"
                                   placeholder="예: 회사 소개, 개인정보처리방침">
                        </div>
                        <div class="wp-form-group">
                            <label class="wp-form-label">URL *</label>
                            <input type="text" name="url" class="wp-form-input" required maxlength="300"
                                   placeholder="예: /page/about, /page/privacy">
                        </div>
                        <div class="wp-form-group">
                            <label class="wp-form-label">열기 방식</label>
                            <select name="target" class="wp-form-input wp-form-select">
                                <option value="_self">현재 창</option>
                                <option value="_blank">새 창</option>
                            </select>
                        </div>
                        <button type="submit" class="wp-btn wp-btn-primary" style="width:100%;">추가</button>
                    </form>
                </div>
            </div>

            <div class="wp-widget" style="margin-top:12px;">
                <div class="wp-widget-header">빠른 추가</div>
                <div class="wp-widget-body">
                    @php $pages = App\Models\Page::where('is_active', true)->orderBy('order')->get(); @endphp
                    @if($pages->isNotEmpty())
                    <p style="font-size:11px;font-weight:700;color:#1d2327;margin-bottom:6px;text-transform:uppercase;letter-spacing:.04em;">페이지</p>
                    @foreach($pages as $pg)
                    <form method="POST" action="{{ route('admin.theme.footer-menu.store') }}" style="display:inline;">
                        @csrf
                        <input type="hidden" name="label" value="{{ $pg->title }}">
                        <input type="hidden" name="url" value="/page/{{ $pg->slug }}">
                        <input type="hidden" name="group" value="안내">
                        <input type="hidden" name="target" value="_self">
                        <button type="submit" class="wp-btn wp-btn-secondary wp-btn-sm" style="margin-bottom:4px;">+ {{ $pg->title }}</button>
                    </form>
                    @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 푸터 메뉴 수정 모달 --}}
<div id="footer-edit-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:4px;width:400px;max-width:90vw;box-shadow:0 4px 20px rgba(0,0,0,.3);">
        <div style="padding:14px 16px;border-bottom:1px solid #c3c4c7;font-weight:700;font-size:14px;display:flex;justify-content:space-between;">
            <span>푸터 메뉴 수정</span>
            <button type="button" onclick="closeFooterEditModal()" style="background:none;border:none;cursor:pointer;font-size:18px;color:#646970;">×</button>
        </div>
        <form id="footer-edit-form" method="POST" style="padding:16px;">
            @csrf @method('PUT')
            <div class="wp-form-group">
                <label class="wp-form-label">그룹명</label>
                <input type="text" name="group" id="fedit-group" class="wp-form-input" required>
            </div>
            <div class="wp-form-group">
                <label class="wp-form-label">메뉴명</label>
                <input type="text" name="label" id="fedit-label" class="wp-form-input" required>
            </div>
            <div class="wp-form-group">
                <label class="wp-form-label">URL</label>
                <input type="text" name="url" id="fedit-url" class="wp-form-input" required>
            </div>
            <div class="wp-form-group">
                <label class="wp-form-label">열기 방식</label>
                <select name="target" id="fedit-target" class="wp-form-input wp-form-select">
                    <option value="_self">현재 창</option>
                    <option value="_blank">새 창</option>
                </select>
            </div>
            <div class="wp-form-group">
                <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer;">
                    <input type="checkbox" name="is_active" id="fedit-active" value="1"> 활성화
                </label>
            </div>
            <div style="display:flex;gap:8px;">
                <button type="submit" class="wp-btn wp-btn-primary">저장</button>
                <button type="button" onclick="closeFooterEditModal()" class="wp-btn wp-btn-secondary">취소</button>
            </div>
        </form>
    </div>
</div>

{{-- ══ 색상 설정 탭 ════════════════════════════════════════════ --}}
<div id="tab-color" style="display:none;">

{{-- 프리셋 --}}
<div class="wp-widget" style="margin-bottom:20px;">
    <div class="wp-widget-header">프리셋 테마</div>
    <div class="wp-widget-body" style="display:flex;flex-wrap:wrap;gap:8px;">
        @php
        $presets = [
            '클래식 블루'  => ['#1a6fb5','#e8524a','#e8524a','#ffffff','#e8f4fd','#1e3a5f','#f5f5f5','#1a1a1a'],
            '퍼플'         => ['#7c3aed','#e8524a','#7c3aed','#ffffff','#ffffff','#4b5563','#f9fafb','#1f2937'],
            '다크 뉴스'    => ['#38bdf8','#f97316','#0f172a','#e2e8f0','#1e293b','#cbd5e1','#0f172a','#e2e8f0'],
            '포레스트 그린'=> ['#16a34a','#ca8a04','#15803d','#ffffff','#f0fdf4','#14532d','#f7fef9','#1a1a1a'],
            '레드 저널'    => ['#dc2626','#1d4ed8','#b91c1c','#ffffff','#fff5f5','#7f1d1d','#fffbfb','#1a1a1a'],
            '오렌지'       => ['#ea7117','#0ea5e9','#ea7117','#ffffff','#fff7ed','#7c2d12','#fffbf5','#1a1a1a'],
        ];
        @endphp
        @foreach($presets as $name => $vals)
        <button type="button"
                onclick="applyPreset('{{ $vals[0] }}','{{ $vals[1] }}','{{ $vals[2] }}','{{ $vals[3] }}','{{ $vals[4] }}','{{ $vals[5] }}','{{ $vals[6] }}','{{ $vals[7] }}')"
                style="display:flex;align-items:center;gap:6px;padding:6px 12px;border:1px solid #c3c4c7;border-radius:3px;background:#fff;cursor:pointer;font-size:12px;font-weight:600;color:#1d2327;">
            <span style="display:inline-flex;gap:2px;">
                <span style="width:12px;height:12px;border-radius:2px;background:{{ $vals[0] }};display:inline-block;"></span>
                <span style="width:12px;height:12px;border-radius:2px;background:{{ $vals[1] }};display:inline-block;"></span>
                <span style="width:12px;height:12px;border-radius:2px;background:{{ $vals[2] }};display:inline-block;"></span>
            </span>
            {{ $name }}
        </button>
        @endforeach
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 320px;gap:20px;align-items:start;">

    {{-- 색상 설정 폼 --}}
    <form method="POST" action="{{ route('admin.theme.color.update') }}" id="color-form">
        @csrf
        <div class="wp-widget">
            <div class="wp-widget-header">색상 설정</div>
            <div class="wp-widget-body">
                @php
                $colorFields = [
                    'theme_primary'     => ['주요 색상', '버튼, 링크, 활성 메뉴, 뱃지 등 전반에 사용'],
                    'theme_accent'      => ['강조 색상', '속보 뱃지, 특별 하이라이트'],
                    'theme_topbar_bg'   => ['상단 알림바 배경', '네비게이션 상단 알림 띠'],
                    'theme_topbar_text' => ['상단 알림바 텍스트', '알림 띠 안의 텍스트'],
                    'theme_nav_bg'      => ['네비게이션 배경', '메뉴바 배경 색상'],
                    'theme_nav_text'    => ['네비게이션 텍스트', '메뉴 링크 기본 색상'],
                    'theme_site_bg'     => ['사이트 배경', '페이지 전체 배경'],
                    'theme_text'        => ['본문 텍스트', '일반 텍스트 기본 색상'],
                ];
                @endphp
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 20px;">
                @foreach($colorFields as $key => [$label, $desc])
                <div class="wp-form-group">
                    <label class="wp-form-label">{{ $label }}</label>
                    <div style="display:flex;gap:6px;align-items:center;">
                        <input type="color" id="cp-{{ $key }}" value="{{ $colors[$key] }}"
                               oninput="document.getElementById('ch-{{ $key }}').value=this.value;updateColorPreview();"
                               style="width:40px;height:34px;padding:2px;border:1px solid #8c8f94;border-radius:3px;cursor:pointer;flex-shrink:0;">
                        <input type="text" name="{{ $key }}" id="ch-{{ $key }}" value="{{ $colors[$key] }}"
                               maxlength="20"
                               oninput="document.getElementById('cp-{{ $key }}').value=this.value;updateColorPreview();"
                               style="flex:1;padding:6px 8px;font-size:12px;border:1px solid #8c8f94;border-radius:3px;font-family:monospace;">
                    </div>
                    <p style="font-size:11px;color:#8c8f94;margin-top:3px;">{{ $desc }}</p>
                </div>
                @endforeach
                </div>
            </div>
        </div>
        <div style="margin-top:12px;">
            <button type="submit" class="wp-btn wp-btn-primary">색상 저장</button>
            <button type="button" onclick="resetColors()" class="wp-btn wp-btn-secondary" style="margin-left:8px;">기본값으로 초기화</button>
        </div>
    </form>

    {{-- 실시간 미리보기 --}}
    <div>
        <div class="wp-widget" style="position:sticky;top:52px;">
            <div class="wp-widget-header">실시간 미리보기</div>
            <div class="wp-widget-body" style="padding:0;overflow:hidden;border-radius:0 0 3px 3px;">
                {{-- 탑바 --}}
                <div id="prev-topbar" style="padding:5px 12px;font-size:11px;font-weight:600;text-align:center;background:{{ $colors['theme_topbar_bg'] }};color:{{ $colors['theme_topbar_text'] }};">
                    📢 사이트 공지사항
                </div>
                {{-- 네비 --}}
                <div id="prev-nav" style="padding:8px 12px;display:flex;align-items:center;gap:8px;border-bottom:2px solid {{ $colors['theme_primary'] }};background:{{ $colors['theme_nav_bg'] }};">
                    <span style="font-size:14px;font-weight:900;color:{{ $colors['theme_primary'] }};">LOGO</span>
                    <span style="flex:1;"></span>
                    <span id="prev-nav-link" style="font-size:12px;font-weight:600;color:{{ $colors['theme_nav_text'] }};">메뉴</span>
                    <span style="font-size:12px;font-weight:700;padding:3px 8px;border-radius:3px;background:{{ $colors['theme_primary'] }};color:#fff;">활성 메뉴</span>
                    <span style="font-size:11px;padding:2px 8px;border-radius:3px;background:{{ $colors['theme_primary'] }};color:#fff;">버튼</span>
                </div>
                {{-- 본문 미리보기 --}}
                <div id="prev-body" style="padding:12px;background:{{ $colors['theme_site_bg'] }};min-height:80px;">
                    <p style="font-size:12px;font-weight:700;color:{{ $colors['theme_primary'] }};margin-bottom:4px;">섹션 제목</p>
                    <p id="prev-text" style="font-size:12px;color:{{ $colors['theme_text'] }};line-height:1.6;">본문 텍스트 미리보기입니다. 전체적인 색상 조합을 확인하세요.</p>
                    <span style="display:inline-block;margin-top:6px;font-size:11px;font-weight:700;padding:2px 6px;border-radius:2px;background:{{ $colors['theme_accent'] }};color:#fff;">강조 뱃지</span>
                </div>
            </div>
        </div>
    </div>

</div>
</div>

{{-- ══ 위젯 설정 탭 ═══════════════════════════════════════════ --}}
<div id="tab-widget" style="display:none;">
@php
$allMainWidgets = [
    'hero_articles'     => '히어로 기사 (최신 대표 기사)',
    'category_articles' => '카테고리별 기사',
    'latest_articles'   => '최신 기사 목록',
    'board_sections'    => '게시판별 섹션',
    'stats'             => '사이트 통계',
];
$allSidebarWidgets = [
    'login'             => '로그인 / 회원 정보',
    'notice'            => '공지사항',
    'popular_articles'  => '인기 기사',
    'popular_posts'     => '인기 게시글',
    'boards'            => '게시판 바로가기',
];
@endphp

<form method="POST" action="{{ route('admin.theme.widget.update') }}">
    @csrf
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

        {{-- 메인 영역 위젯 --}}
        <div class="wp-widget">
            <div class="wp-widget-header">메인 영역 위젯</div>
            <div class="wp-widget-body">
                <p style="font-size:12px;color:#646970;margin-bottom:14px;">체크 및 드래그로 홈 메인 영역에 표시할 위젯과 순서를 설정합니다.</p>
                <ul id="main-widget-list" style="list-style:none;padding:0;margin:0;">
                    @php
                        $orderedMain = array_merge(
                            array_intersect($mainWidgets, array_keys($allMainWidgets)),
                            array_diff(array_keys($allMainWidgets), $mainWidgets)
                        );
                    @endphp
                    @foreach($orderedMain as $wid)
                    <li style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid #f0f0f1;cursor:grab;" class="widget-row">
                        <span style="color:#c3c4c7;flex-shrink:0;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 6h8M8 12h8M8 18h8" stroke-width="2.5" stroke-linecap="round"/></svg>
                        </span>
                        <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer;flex:1;">
                            <input type="checkbox" name="main_widgets[]" value="{{ $wid }}"
                                   {{ in_array($wid, $mainWidgets) ? 'checked' : '' }}>
                            {{ $allMainWidgets[$wid] }}
                        </label>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- 사이드바 위젯 --}}
        <div class="wp-widget">
            <div class="wp-widget-header">사이드바 위젯</div>
            <div class="wp-widget-body">
                <p style="font-size:12px;color:#646970;margin-bottom:14px;">체크 및 드래그로 사이드바에 표시할 위젯과 순서를 설정합니다.</p>
                <ul id="sidebar-widget-list" style="list-style:none;padding:0;margin:0;">
                    @php
                        $orderedSidebar = array_merge(
                            array_intersect($sidebarWidgets, array_keys($allSidebarWidgets)),
                            array_diff(array_keys($allSidebarWidgets), $sidebarWidgets)
                        );
                    @endphp
                    @foreach($orderedSidebar as $wid)
                    <li style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid #f0f0f1;cursor:grab;" class="widget-row">
                        <span style="color:#c3c4c7;flex-shrink:0;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 6h8M8 12h8M8 18h8" stroke-width="2.5" stroke-linecap="round"/></svg>
                        </span>
                        <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer;flex:1;">
                            <input type="checkbox" name="sidebar_widgets[]" value="{{ $wid }}"
                                   {{ in_array($wid, $sidebarWidgets) ? 'checked' : '' }}>
                            {{ $allSidebarWidgets[$wid] }}
                        </label>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <div style="margin-top:16px;">
        <button type="submit" class="wp-btn wp-btn-primary">위젯 설정 저장</button>
    </div>
</form>
</div>

{{-- 메뉴 수정 모달 --}}
<div id="edit-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:4px;width:400px;max-width:90vw;box-shadow:0 4px 20px rgba(0,0,0,.3);">
        <div style="padding:14px 16px;border-bottom:1px solid #c3c4c7;font-weight:700;font-size:14px;display:flex;justify-content:space-between;">
            <span>메뉴 수정</span>
            <button type="button" onclick="closeEditModal()" style="background:none;border:none;cursor:pointer;font-size:18px;color:#646970;">×</button>
        </div>
        <form id="edit-form" method="POST" style="padding:16px;">
            @csrf @method('PUT')
            <div class="wp-form-group">
                <label class="wp-form-label">메뉴명</label>
                <input type="text" name="label" id="edit-label" class="wp-form-input" required>
            </div>
            <div class="wp-form-group">
                <label class="wp-form-label">URL</label>
                <input type="text" name="url" id="edit-url" class="wp-form-input" required>
            </div>
            <div class="wp-form-group">
                <label class="wp-form-label">열기 방식</label>
                <select name="target" id="edit-target" class="wp-form-input wp-form-select">
                    <option value="_self">현재 창</option>
                    <option value="_blank">새 창</option>
                </select>
            </div>
            <div class="wp-form-group">
                <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer;">
                    <input type="checkbox" name="is_active" id="edit-active" value="1"> 활성화
                </label>
            </div>
            <div style="display:flex;gap:8px;">
                <button type="submit" class="wp-btn wp-btn-primary">저장</button>
                <button type="button" onclick="closeEditModal()" class="wp-btn wp-btn-secondary">취소</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// ── 탭 전환 ──────────────────────────────────────────────────
var tabIds = ['tab-menu', 'tab-footer', 'tab-widget', 'tab-color'];
document.querySelectorAll('.theme-tab').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.theme-tab').forEach(function(b) {
            b.style.borderBottomColor = 'transparent';
            b.style.color = '#646970';
        });
        this.style.borderBottomColor = '#2271b1';
        this.style.color = '#2271b1';

        tabIds.forEach(function(id) { document.getElementById(id).style.display = 'none'; });
        document.getElementById(this.dataset.target).style.display = 'block';
    });
});

// ── 메뉴 모달 ──────────────────────────────────────────────
function openEditModal(id, label, url, target, isActive) {
    document.getElementById('edit-form').action = '/admin/theme/menu/' + id;
    document.getElementById('edit-label').value  = label;
    document.getElementById('edit-url').value    = url;
    document.getElementById('edit-target').value = target;
    document.getElementById('edit-active').checked = isActive;
    document.getElementById('edit-modal').style.display = 'flex';
}
function closeEditModal() {
    document.getElementById('edit-modal').style.display = 'none';
}

// ── 드래그 & 드롭 순서 변경 ──────────────────────────────────
function makeSortable(listId, saveUrl) {
    var list = document.getElementById(listId);
    if (!list) return;
    var dragging = null;

    list.querySelectorAll('li').forEach(function(item) {
        item.setAttribute('draggable', 'true');

        item.addEventListener('dragstart', function(e) {
            dragging = this;
            this.style.opacity = '.4';
        });
        item.addEventListener('dragend', function() {
            this.style.opacity = '';
            if (saveUrl) saveOrder(list, saveUrl);
        });
        item.addEventListener('dragover', function(e) {
            e.preventDefault();
            var rect = this.getBoundingClientRect();
            var mid  = rect.top + rect.height / 2;
            if (e.clientY < mid) {
                list.insertBefore(dragging, this);
            } else {
                list.insertBefore(dragging, this.nextSibling);
            }
        });
    });
}

function saveOrder(list, url) {
    var ids = Array.from(list.querySelectorAll('li[data-id]')).map(function(li) { return li.dataset.id; });
    if (!ids.length) return;
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ ids: ids }),
    });
}

// ── 위젯 드래그 (순서만, 저장은 폼 제출) ─────────────────────
function makeWidgetSortable(listId) {
    var list = document.getElementById(listId);
    if (!list) return;
    var dragging = null;

    list.querySelectorAll('li').forEach(function(item) {
        item.setAttribute('draggable', 'true');
        item.addEventListener('dragstart', function() { dragging = this; this.style.opacity = '.4'; });
        item.addEventListener('dragend',   function() { this.style.opacity = ''; });
        item.addEventListener('dragover',  function(e) {
            e.preventDefault();
            var rect = this.getBoundingClientRect();
            list.insertBefore(dragging, e.clientY < rect.top + rect.height / 2 ? this : this.nextSibling);
        });
    });
}

makeSortable('menu-sortable', '/admin/theme/menu/reorder');
makeWidgetSortable('main-widget-list');
makeWidgetSortable('sidebar-widget-list');

// ── 색상 설정 미리보기 ────────────────────────────────────────
function getVal(key) { return document.getElementById('ch-' + key)?.value || ''; }
function updateColorPreview() {
    var p = document.getElementById('prev-primary-text');
    var topbar  = document.getElementById('prev-topbar');
    var nav     = document.getElementById('prev-nav');
    var navLink = document.getElementById('prev-nav-link');
    var body    = document.getElementById('prev-body');
    var text    = document.getElementById('prev-text');
    if (!topbar) return;
    var primary = getVal('theme_primary');
    var accent  = getVal('theme_accent');
    var tbBg    = getVal('theme_topbar_bg');
    var tbText  = getVal('theme_topbar_text');
    var navBg   = getVal('theme_nav_bg');
    var navText = getVal('theme_nav_text');
    var siteBg  = getVal('theme_site_bg');
    var siteText= getVal('theme_text');

    topbar.style.background = tbBg;
    topbar.style.color      = tbText;
    nav.style.background    = navBg;
    nav.style.borderBottomColor = primary;
    navLink.style.color     = navText;
    // update active nav chip & button colors
    nav.querySelectorAll('span[style*="background"]').forEach(function(el, i) {
        el.style.background = primary;
    });
    nav.querySelector('span[style*="font-weight:900"]').style.color = primary;
    body.style.background   = siteBg;
    text.style.color        = siteText;
    // accent badge
    var badge = body.querySelector('span[style*="border-radius"]');
    if (badge) badge.style.background = accent;
}

function applyPreset(primary, accent, tbBg, tbText, navBg, navText, siteBg, siteText) {
    var map = {
        theme_primary: primary, theme_accent: accent,
        theme_topbar_bg: tbBg, theme_topbar_text: tbText,
        theme_nav_bg: navBg, theme_nav_text: navText,
        theme_site_bg: siteBg, theme_text: siteText,
    };
    Object.entries(map).forEach(function([k, v]) {
        var picker = document.getElementById('cp-' + k);
        var hex    = document.getElementById('ch-' + k);
        if (picker) picker.value = v;
        if (hex)    hex.value    = v;
    });
    updateColorPreview();
}

var defaultColors = {
    theme_primary: '{{ $colors['theme_primary'] }}', theme_accent: '{{ $colors['theme_accent'] }}',
    theme_topbar_bg: '{{ $colors['theme_topbar_bg'] }}', theme_topbar_text: '{{ $colors['theme_topbar_text'] }}',
    theme_nav_bg: '{{ $colors['theme_nav_bg'] }}', theme_nav_text: '{{ $colors['theme_nav_text'] }}',
    theme_site_bg: '{{ $colors['theme_site_bg'] }}', theme_text: '{{ $colors['theme_text'] }}',
};
function resetColors() {
    if (!confirm('저장된 색상을 기본값으로 되돌리시겠습니까?')) return;
    applyPreset(
        defaultColors.theme_primary, defaultColors.theme_accent,
        defaultColors.theme_topbar_bg, defaultColors.theme_topbar_text,
        defaultColors.theme_nav_bg, defaultColors.theme_nav_text,
        defaultColors.theme_site_bg, defaultColors.theme_text
    );
}

// ── 푸터 메뉴 모달 ────────────────────────────────────────────
function openFooterEditModal(id, label, url, group, target, isActive) {
    document.getElementById('footer-edit-form').action = '/admin/theme/footer-menu/' + id;
    document.getElementById('fedit-group').value   = group;
    document.getElementById('fedit-label').value   = label;
    document.getElementById('fedit-url').value     = url;
    document.getElementById('fedit-target').value  = target;
    document.getElementById('fedit-active').checked = isActive;
    document.getElementById('footer-edit-modal').style.display = 'flex';
}
function closeFooterEditModal() {
    document.getElementById('footer-edit-modal').style.display = 'none';
}
</script>
@endpush
@endsection
