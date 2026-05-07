<!DOCTYPE html>
<html lang="ko" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LaraPress | 시작 가이드</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        pre { tab-size: 2; }
        .sidebar-link.active { color: #4f46e5; font-weight: 700; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased">

    <div class="flex min-h-screen">

        <!-- 사이드바 -->
        <aside class="hidden lg:flex flex-col w-64 fixed top-0 left-0 h-full bg-white border-r border-slate-200 z-40">
            <div class="px-6 py-5 border-b border-slate-100">
                <span class="text-xl font-black text-indigo-600 tracking-tighter">LARAPRESS</span>
                <span class="ml-2 text-xs text-slate-400 font-medium">시작 가이드</span>
            </div>
            <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1 text-sm text-slate-600">
                <a href="#overview"       class="sidebar-link block px-3 py-2 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition">개요</a>
                <a href="#requirements"   class="sidebar-link block px-3 py-2 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition">시스템 요구사항</a>
                <a href="#install"        class="sidebar-link block px-3 py-2 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition">설치</a>
                <a href="#env"            class="sidebar-link block px-3 py-2 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition">환경 설정 (.env)</a>
                <a href="#landing"        class="sidebar-link block px-3 py-2 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition">랜딩 페이지 설정</a>
                <a href="#admin"          class="sidebar-link block px-3 py-2 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition">관리자 패널</a>
                <a href="#boards"         class="sidebar-link block px-3 py-2 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition">게시판 관리</a>
                <a href="#articles"       class="sidebar-link block px-3 py-2 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition">기사 작성</a>
                <a href="#skins"          class="sidebar-link block px-3 py-2 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition">스킨 & 레이아웃</a>
                <a href="#plugins"        class="sidebar-link block px-3 py-2 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition">플러그인</a>
                <a href="#seo"            class="sidebar-link block px-3 py-2 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition">SEO 설정</a>
                <a href="#users"          class="sidebar-link block px-3 py-2 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition">사용자 & 권한</a>
                <a href="#commands"       class="sidebar-link block px-3 py-2 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition">주요 명령어</a>
            </nav>
            <div class="px-6 py-4 border-t border-slate-100 text-xs text-slate-400">
                &copy; 2026 LaraPress
            </div>
        </aside>

        <!-- 본문 -->
        <main class="lg:ml-64 flex-1 max-w-4xl mx-auto px-6 py-16 lg:px-12">

            <!-- 헤더 -->
            <div class="mb-16">
                <span class="inline-block bg-indigo-100 text-indigo-700 text-xs font-bold px-3 py-1 rounded-full mb-4">Documentation</span>
                <h1 class="text-4xl font-black text-slate-900 mb-4">LaraPress 시작 가이드</h1>
                <p class="text-lg text-slate-500 leading-relaxed">라라벨 기반 뉴스 CMS. 설치부터 기사 발행까지 빠르게 시작하세요.</p>
            </div>

            <div class="space-y-20">

                <!-- 개요 -->
                <section id="overview">
                    <h2 class="text-2xl font-black text-slate-900 mb-6 pb-3 border-b border-slate-200">개요</h2>
                    <p class="text-slate-600 leading-relaxed mb-4">LaraPress는 Laravel 10 기반의 뉴스/미디어 특화 CMS입니다. 멀티 게시판, 스킨 시스템, 관리자 패널, SEO 최적화, RSS 피드, 사이트맵을 기본 제공합니다.</p>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                        <div class="bg-white border border-slate-200 rounded-xl p-4 text-center">
                            <div class="text-2xl mb-1">🗞️</div>
                            <div class="text-xs font-bold text-slate-700">뉴스 CMS</div>
                        </div>
                        <div class="bg-white border border-slate-200 rounded-xl p-4 text-center">
                            <div class="text-2xl mb-1">🎨</div>
                            <div class="text-xs font-bold text-slate-700">스킨 시스템</div>
                        </div>
                        <div class="bg-white border border-slate-200 rounded-xl p-4 text-center">
                            <div class="text-2xl mb-1">🔌</div>
                            <div class="text-xs font-bold text-slate-700">플러그인</div>
                        </div>
                        <div class="bg-white border border-slate-200 rounded-xl p-4 text-center">
                            <div class="text-2xl mb-1">📡</div>
                            <div class="text-xs font-bold text-slate-700">RSS / 사이트맵</div>
                        </div>
                    </div>
                </section>

                <!-- 시스템 요구사항 -->
                <section id="requirements">
                    <h2 class="text-2xl font-black text-slate-900 mb-6 pb-3 border-b border-slate-200">시스템 요구사항</h2>
                    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="text-left px-5 py-3 font-bold text-slate-700">항목</th>
                                    <th class="text-left px-5 py-3 font-bold text-slate-700">최소 버전</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr><td class="px-5 py-3 text-slate-600">PHP</td><td class="px-5 py-3 font-mono text-slate-800">8.1 이상</td></tr>
                                <tr><td class="px-5 py-3 text-slate-600">Laravel</td><td class="px-5 py-3 font-mono text-slate-800">10.x</td></tr>
                                <tr><td class="px-5 py-3 text-slate-600">MySQL / MariaDB</td><td class="px-5 py-3 font-mono text-slate-800">8.0 / 10.4 이상</td></tr>
                                <tr><td class="px-5 py-3 text-slate-600">Node.js</td><td class="px-5 py-3 font-mono text-slate-800">18 이상 (빌드 전용)</td></tr>
                                <tr><td class="px-5 py-3 text-slate-600">Composer</td><td class="px-5 py-3 font-mono text-slate-800">2.x</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- 설치 -->
                <section id="install">
                    <h2 class="text-2xl font-black text-slate-900 mb-6 pb-3 border-b border-slate-200">설치</h2>
                    <ol class="space-y-6">
                        <li>
                            <p class="font-bold text-slate-800 mb-2">1. 저장소 클론</p>
                            <pre class="bg-slate-900 text-green-400 text-sm rounded-xl p-5 overflow-x-auto">git clone https://github.com/your-org/larapress.git
cd larapress</pre>
                        </li>
                        <li>
                            <p class="font-bold text-slate-800 mb-2">2. 의존성 설치 및 초기 설정</p>
                            <pre class="bg-slate-900 text-green-400 text-sm rounded-xl p-5 overflow-x-auto">composer setup        # .env 복사, autoload 덤프, 앱 키 생성
npm install
npm run build         # 프로덕션 에셋 빌드</pre>
                        </li>
                        <li>
                            <p class="font-bold text-slate-800 mb-2">3. 데이터베이스 마이그레이션</p>
                            <pre class="bg-slate-900 text-green-400 text-sm rounded-xl p-5 overflow-x-auto">php artisan migrate</pre>
                        </li>
                        <li>
                            <p class="font-bold text-slate-800 mb-2">4. 관리자 계정 생성</p>
                            <pre class="bg-slate-900 text-green-400 text-sm rounded-xl p-5 overflow-x-auto">php artisan tinker
>>> \App\Models\User::create([
...   'name' => '관리자',
...   'email' => 'admin@example.com',
...   'password' => bcrypt('비밀번호'),
...   'is_admin' => true,
... ]);</pre>
                        </li>
                    </ol>
                    <div class="mt-6 bg-amber-50 border border-amber-200 rounded-xl p-5 text-sm text-amber-800">
                        <strong>문서 루트 주의:</strong> 이 프로젝트의 웹서버 document root는 <code class="bg-amber-100 px-1 rounded">/public</code>이 아닌 <strong>프로젝트 루트</strong>입니다. Nginx/Apache 설정 시 루트를 프로젝트 최상위 디렉터리로 지정하세요.
                    </div>
                </section>

                <!-- 환경 설정 -->
                <section id="env">
                    <h2 class="text-2xl font-black text-slate-900 mb-6 pb-3 border-b border-slate-200">환경 설정 (.env)</h2>
                    <p class="text-slate-600 mb-4">주요 환경 변수입니다. <code class="bg-slate-100 px-2 py-0.5 rounded font-mono text-sm">.env</code> 파일에서 설정하세요.</p>
                    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden text-sm">
                        <table class="w-full">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="text-left px-5 py-3 font-bold text-slate-700">키</th>
                                    <th class="text-left px-5 py-3 font-bold text-slate-700">설명</th>
                                    <th class="text-left px-5 py-3 font-bold text-slate-700">기본값</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-600">
                                <tr>
                                    <td class="px-5 py-3 font-mono text-indigo-700">APP_NAME</td>
                                    <td class="px-5 py-3">사이트 이름</td>
                                    <td class="px-5 py-3 font-mono">Laraboard</td>
                                </tr>
                                <tr>
                                    <td class="px-5 py-3 font-mono text-indigo-700">APP_URL</td>
                                    <td class="px-5 py-3">사이트 URL</td>
                                    <td class="px-5 py-3 font-mono">http://localhost</td>
                                </tr>
                                <tr>
                                    <td class="px-5 py-3 font-mono text-indigo-700">DB_DATABASE</td>
                                    <td class="px-5 py-3">데이터베이스명</td>
                                    <td class="px-5 py-3 font-mono">laraboard</td>
                                </tr>
                                <tr>
                                    <td class="px-5 py-3 font-mono text-indigo-700">MAIL_MAILER</td>
                                    <td class="px-5 py-3">메일 드라이버</td>
                                    <td class="px-5 py-3 font-mono">smtp</td>
                                </tr>
                                <tr>
                                    <td class="px-5 py-3 font-mono text-indigo-700">SHOW_LANDING_PAGE</td>
                                    <td class="px-5 py-3">루트(/) 랜딩 페이지 표시 여부</td>
                                    <td class="px-5 py-3 font-mono">false</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- 랜딩 페이지 설정 -->
                <section id="landing">
                    <h2 class="text-2xl font-black text-slate-900 mb-6 pb-3 border-b border-slate-200">랜딩 페이지 설정</h2>
                    <p class="text-slate-600 mb-4">루트 URL(<code class="bg-slate-100 px-2 py-0.5 rounded font-mono text-sm">/</code>) 접속 시 랜딩 페이지를 보여줄지, 바로 CMS 홈으로 이동할지 제어할 수 있습니다.</p>
                    <pre class="bg-slate-900 text-green-400 text-sm rounded-xl p-5 overflow-x-auto"># .env

SHOW_LANDING_PAGE=true   # 랜딩 페이지(welcome.blade.php) 표시
SHOW_LANDING_PAGE=false  # CMS 홈으로 바로 이동 (기본값)</pre>
                    <p class="mt-4 text-sm text-slate-500">설정 변경 후 캐시 초기화: <code class="bg-slate-100 px-2 py-0.5 rounded font-mono">php artisan config:clear</code></p>
                    <div class="mt-4 bg-blue-50 border border-blue-200 rounded-xl p-5 text-sm text-blue-800">
                        랜딩 페이지를 커스터마이징하려면 <code class="bg-blue-100 px-1 rounded">resources/views/welcome.blade.php</code>를 수정하세요. 이 파일은 <code>.gitignore</code>에 포함되어 있어 커밋되지 않습니다.
                    </div>
                </section>

                <!-- 관리자 패널 -->
                <section id="admin">
                    <h2 class="text-2xl font-black text-slate-900 mb-6 pb-3 border-b border-slate-200">관리자 패널</h2>
                    <p class="text-slate-600 mb-4"><code class="bg-slate-100 px-2 py-0.5 rounded font-mono text-sm">/admin</code>으로 접속합니다. <code class="bg-slate-100 px-2 py-0.5 rounded font-mono text-sm">is_admin = true</code>인 계정만 접근 가능합니다.</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-white border border-slate-200 rounded-xl p-5">
                            <h3 class="font-bold text-slate-800 mb-3">주요 메뉴</h3>
                            <ul class="space-y-2 text-sm text-slate-600">
                                <li><span class="font-mono text-indigo-600">/admin</span> — 대시보드</li>
                                <li><span class="font-mono text-indigo-600">/admin/articles</span> — 기사 관리</li>
                                <li><span class="font-mono text-indigo-600">/admin/boards</span> — 게시판 관리</li>
                                <li><span class="font-mono text-indigo-600">/admin/users</span> — 사용자 관리</li>
                                <li><span class="font-mono text-indigo-600">/admin/settings</span> — 사이트 설정</li>
                                <li><span class="font-mono text-indigo-600">/admin/plugins</span> — 플러그인</li>
                                <li><span class="font-mono text-indigo-600">/admin/statistics</span> — 통계</li>
                                <li><span class="font-mono text-indigo-600">/admin/seo</span> — SEO 설정</li>
                                <li><span class="font-mono text-indigo-600">/admin/theme</span> — 테마/스킨</li>
                                <li><span class="font-mono text-indigo-600">/admin/top-banners</span> — 상단 배너</li>
                            </ul>
                        </div>
                        <div class="bg-white border border-slate-200 rounded-xl p-5">
                            <h3 class="font-bold text-slate-800 mb-3">권한 구조</h3>
                            <ul class="space-y-2 text-sm text-slate-600">
                                <li><strong>admin</strong> — 전체 관리 권한</li>
                                <li><strong>author</strong> — 기사/게시글 작성 가능</li>
                                <li><strong>subscriber</strong> — 로그인 후 열람만 가능</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <!-- 게시판 관리 -->
                <section id="boards">
                    <h2 class="text-2xl font-black text-slate-900 mb-6 pb-3 border-b border-slate-200">게시판 관리</h2>
                    <p class="text-slate-600 mb-4">관리자 패널 → <strong>게시판 관리</strong>에서 게시판을 생성/수정할 수 있습니다.</p>
                    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden text-sm">
                        <table class="w-full">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="text-left px-5 py-3 font-bold text-slate-700">필드</th>
                                    <th class="text-left px-5 py-3 font-bold text-slate-700">설명</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-600">
                                <tr><td class="px-5 py-3 font-mono">board_id</td><td class="px-5 py-3">URL 슬러그 (예: <code>free</code> → <code>/bbs/free</code>)</td></tr>
                                <tr><td class="px-5 py-3 font-mono">name</td><td class="px-5 py-3">게시판 이름</td></tr>
                                <tr><td class="px-5 py-3 font-mono">skin</td><td class="px-5 py-3">게시판 스킨 (기본: basic)</td></tr>
                                <tr><td class="px-5 py-3 font-mono">posts_per_page</td><td class="px-5 py-3">페이지당 게시글 수</td></tr>
                                <tr><td class="px-5 py-3 font-mono">allow_comments</td><td class="px-5 py-3">댓글 허용 여부</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- 기사 작성 -->
                <section id="articles">
                    <h2 class="text-2xl font-black text-slate-900 mb-6 pb-3 border-b border-slate-200">기사 작성</h2>
                    <p class="text-slate-600 mb-4">관리자 패널 → <strong>기사 관리</strong>에서 작성합니다. author 권한 이상의 계정이 필요합니다.</p>
                    <ul class="space-y-3 text-sm text-slate-600">
                        <li class="flex gap-3"><span class="text-indigo-500 font-bold mt-0.5">•</span><span><strong>제목 / 부제목</strong> — 기사 제목과 부제목을 입력합니다.</span></li>
                        <li class="flex gap-3"><span class="text-indigo-500 font-bold mt-0.5">•</span><span><strong>본문</strong> — 리치 텍스트 에디터로 작성합니다. 이미지 업로드 지원.</span></li>
                        <li class="flex gap-3"><span class="text-indigo-500 font-bold mt-0.5">•</span><span><strong>대표 이미지</strong> — 미디어 피커로 업로드 또는 기존 이미지 선택.</span></li>
                        <li class="flex gap-3"><span class="text-indigo-500 font-bold mt-0.5">•</span><span><strong>SEO 필드</strong> — meta title, description, OG 태그를 직접 설정 가능.</span></li>
                        <li class="flex gap-3"><span class="text-indigo-500 font-bold mt-0.5">•</span><span><strong>공지 설정</strong> — <code>is_notice</code> 체크 시 게시판 상단 고정.</span></li>
                    </ul>
                </section>

                <!-- 스킨 & 레이아웃 -->
                <section id="skins">
                    <h2 class="text-2xl font-black text-slate-900 mb-6 pb-3 border-b border-slate-200">스킨 & 레이아웃</h2>
                    <p class="text-slate-600 mb-6">뷰는 데이터베이스 설정에 따라 동적으로 결정됩니다.</p>
                    <div class="space-y-5">
                        <div class="bg-white border border-slate-200 rounded-xl p-5">
                            <h3 class="font-bold text-slate-800 mb-2">레이아웃 스킨</h3>
                            <p class="text-sm text-slate-600 mb-3">사이트 전체 크롬(헤더, 네비, 푸터)을 담당합니다.</p>
                            <code class="text-xs bg-slate-100 px-3 py-1.5 rounded block font-mono">resources/views/skin/layout/{'{skin}'}/</code>
                            <p class="text-xs text-slate-500 mt-2">현재 사용 가능한 스킨: <strong>basic</strong>, <strong>swn-style</strong></p>
                        </div>
                        <div class="bg-white border border-slate-200 rounded-xl p-5">
                            <h3 class="font-bold text-slate-800 mb-2">게시판 스킨</h3>
                            <p class="text-sm text-slate-600 mb-3">게시판별 목록/글보기/쓰기 화면을 담당합니다.</p>
                            <code class="text-xs bg-slate-100 px-3 py-1.5 rounded block font-mono">resources/views/skin/board/{'{skin}'}/</code>
                        </div>
                        <div class="bg-white border border-slate-200 rounded-xl p-5">
                            <h3 class="font-bold text-slate-800 mb-2">활성 스킨 변경</h3>
                            <p class="text-sm text-slate-600">관리자 패널 → <strong>테마 설정</strong>에서 변경하거나, DB에서 직접 설정합니다.</p>
                            <pre class="text-xs bg-slate-900 text-green-400 rounded-lg p-3 mt-2 overflow-x-auto">php artisan tinker
>>> \App\Models\Setting::set('layout_skin', 'swn-style');</pre>
                        </div>
                    </div>
                </section>

                <!-- 플러그인 -->
                <section id="plugins">
                    <h2 class="text-2xl font-black text-slate-900 mb-6 pb-3 border-b border-slate-200">플러그인</h2>
                    <p class="text-slate-600 mb-4">플러그인은 <code class="bg-slate-100 px-2 py-0.5 rounded font-mono text-sm">resources/plugins/{'{plugin-name}'}/</code> 디렉터리에 위치합니다. 관리자 패널 → <strong>플러그인</strong>에서 활성화/비활성화할 수 있습니다.</p>
                    <div class="bg-white border border-slate-200 rounded-xl p-5 text-sm">
                        <h3 class="font-bold text-slate-800 mb-3">기본 포함 플러그인</h3>
                        <ul class="space-y-2 text-slate-600">
                            <li><strong>smtp-mailer</strong> — SMTP 메일 설정 및 테스트 발송</li>
                        </ul>
                        <h3 class="font-bold text-slate-800 mt-5 mb-3">plugin.json 구조</h3>
                        <pre class="bg-slate-900 text-green-400 rounded-lg p-4 overflow-x-auto">{
  "name": "플러그인 이름",
  "version": "1.0.0",
  "description": "플러그인 설명",
  "author": "작성자"
}</pre>
                    </div>
                </section>

                <!-- SEO -->
                <section id="seo">
                    <h2 class="text-2xl font-black text-slate-900 mb-6 pb-3 border-b border-slate-200">SEO 설정</h2>
                    <p class="text-slate-600 mb-4">관리자 패널 → <strong>SEO 설정</strong>에서 사이트 전반의 메타 정보를 설정합니다.</p>
                    <ul class="space-y-3 text-sm text-slate-600">
                        <li class="flex gap-3"><span class="text-indigo-500 font-bold">•</span><span><strong>사이트맵</strong> — <code class="bg-slate-100 px-1 rounded font-mono">/sitemap.xml</code> 자동 생성</span></li>
                        <li class="flex gap-3"><span class="text-indigo-500 font-bold">•</span><span><strong>RSS 피드</strong> — <code class="bg-slate-100 px-1 rounded font-mono">/feed</code>에서 Atom 피드 제공</span></li>
                        <li class="flex gap-3"><span class="text-indigo-500 font-bold">•</span><span><strong>OG 태그</strong> — 기사별 Open Graph 태그 자동 삽입</span></li>
                        <li class="flex gap-3"><span class="text-indigo-500 font-bold">•</span><span><strong>기사별 SEO</strong> — 제목, description, 키워드를 기사 작성 시 개별 설정 가능</span></li>
                    </ul>
                </section>

                <!-- 사용자 & 권한 -->
                <section id="users">
                    <h2 class="text-2xl font-black text-slate-900 mb-6 pb-3 border-b border-slate-200">사용자 & 권한</h2>
                    <p class="text-slate-600 mb-4">관리자 패널 → <strong>사용자 관리</strong>에서 역할을 변경할 수 있습니다.</p>
                    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden text-sm">
                        <table class="w-full">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="text-left px-5 py-3 font-bold text-slate-700">역할</th>
                                    <th class="text-left px-5 py-3 font-bold text-slate-700">권한</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-600">
                                <tr><td class="px-5 py-3 font-bold">admin</td><td class="px-5 py-3">전체 관리 (is_admin = true)</td></tr>
                                <tr><td class="px-5 py-3 font-bold">author</td><td class="px-5 py-3">기사 작성, 게시글 작성, 본인 글 수정/삭제</td></tr>
                                <tr><td class="px-5 py-3 font-bold">subscriber</td><td class="px-5 py-3">로그인 후 열람, 댓글 작성</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- 주요 명령어 -->
                <section id="commands">
                    <h2 class="text-2xl font-black text-slate-900 mb-6 pb-3 border-b border-slate-200">주요 명령어</h2>
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-bold text-slate-500 uppercase mb-2">개발</p>
                            <pre class="bg-slate-900 text-green-400 text-sm rounded-xl p-5 overflow-x-auto">npm run dev            # Vite 개발 서버 (hot reload)
npm run build          # 프로덕션 에셋 빌드
./vendor/bin/pint      # PSR-12 코드 포맷터</pre>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-500 uppercase mb-2">데이터베이스</p>
                            <pre class="bg-slate-900 text-green-400 text-sm rounded-xl p-5 overflow-x-auto">php artisan migrate
php artisan migrate:fresh --seed
php artisan tinker</pre>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-500 uppercase mb-2">캐시</p>
                            <pre class="bg-slate-900 text-green-400 text-sm rounded-xl p-5 overflow-x-auto">php artisan config:clear
php artisan cache:clear
php artisan view:clear</pre>
                        </div>
                    </div>
                </section>

            </div>

            <div class="mt-20 pt-8 border-t border-slate-200 text-center text-xs text-slate-400">
                &copy; 2026 LaraPress Media Solution
            </div>

        </main>
    </div>

    <script>
        // 사이드바 현재 섹션 하이라이트
        const links = document.querySelectorAll('.sidebar-link');
        const observer = new IntersectionObserver(entries => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    links.forEach(l => l.classList.remove('active'));
                    const active = document.querySelector(`.sidebar-link[href="#${e.target.id}"]`);
                    if (active) active.classList.add('active');
                }
            });
        }, { rootMargin: '-20% 0px -70% 0px' });
        document.querySelectorAll('section[id]').forEach(s => observer.observe(s));
    </script>

</body>
</html>
<?php /**PATH /home/laraboard/www/resources/views/welcome.blade.php ENDPATH**/ ?>