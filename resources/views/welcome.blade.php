<!DOCTYPE html>
<html lang="ko" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LaraPress | 5초 완성 언론사 전용 CMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 50%, #ec4899 100%);
            background-size: 200% 200%;
            animation: gradientShift 8s ease infinite;
        }
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased">

    <nav class="fixed top-0 w-full z-50 glass border-b border-slate-200 bg-white/90">
        <div class="max-w-[1600px] mx-auto px-8 h-20 flex items-center justify-between">
            <div class="text-2xl font-black text-indigo-600 tracking-tighter cursor-pointer" onclick="window.scrollTo(0,0)">
                LARAPRESS
            </div>
            <div class="hidden md:flex space-x-10 font-bold text-slate-600 text-base">
                <a href="#features" class="hover:text-indigo-600 transition">차별점</a>
                <a href="#why" class="hover:text-indigo-600 transition">선택 이유</a>
                <a href="#pricing" class="hover:text-indigo-600 transition">이용 플랜</a>
                <a href="#contact" class="hover:text-indigo-600 transition">문의하기</a>
                <a href="/demo" class="hover:text-indigo-600 transition text-indigo-500">데모</a>
            </div>
            <a href="#contact" class="bg-indigo-600 text-white px-6 py-2.5 rounded-xl font-bold hover:bg-indigo-700 transition shadow-lg shadow-indigo-100 text-sm">
                무료 시작
            </a>
        </div>
    </nav>

    <header class="pt-48 pb-28 gradient-bg text-white px-8">
        <div class="max-w-7xl mx-auto text-center">
            <h1 class="text-5xl md:text-6xl font-black mb-8 leading-tight tracking-tight drop-shadow-sm">
                5초면 충분합니다.<br>당신의 언론사를 뚝딱 만드세요.
            </h1>
            <p class="text-lg md:text-xl mb-12 text-indigo-50 font-light leading-relaxed max-w-2xl mx-auto opacity-90">
                무겁고 비싼 구식 CMS의 시대는 끝났습니다. 라라벨 기반의 초고속 퍼포먼스로 독립 미디어의 성공적인 시작을 함께합니다.
            </p>
            <div class="flex flex-col md:flex-row justify-center gap-4">
                <a href="#contact" class="bg-white text-indigo-600 px-8 py-4 rounded-xl font-bold text-lg shadow-xl hover:-translate-y-1 transition text-center">
                    지금 바로 배포하기
                </a>
                <a href="/demo" class="glass text-white px-8 py-4 rounded-xl font-bold text-lg hover:bg-white/10 transition text-center border">
                    데모 체험하기
                </a>
            </div>
        </div>
    </header>

    <section id="features" class="py-24 px-8 bg-white">
        <div class="max-w-[1400px] mx-auto">
            <div class="text-center mb-20">
                <h2 class="text-3xl md:text-4xl font-black text-slate-900 mb-4 tracking-tight">유수 솔루션 업체와 무엇이 다른가요?</h2>
                <p class="text-slate-500 text-lg font-light">오래된 기술의 벽을 허물고 최신 웹 표준을 지향합니다.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <div class="p-10 border-b-4 border-indigo-500 bg-slate-50 rounded-2xl">
                    <div class="text-4xl mb-6">🚀</div>
                    <h3 class="text-xl font-bold mb-4 text-slate-800">압도적 구축 속도</h3>
                    <p class="text-slate-600 leading-relaxed text-sm">기존 업체가 며칠을 소요할 때, LaraPress는 단 5초면 설치됩니다. 자동화된 배포 시스템을 경험하세요.</p>
                </div>
                <div class="p-10 border-b-4 border-pink-500 bg-slate-50 rounded-2xl">
                    <div class="text-4xl mb-6">🔓</div>
                    <h3 class="text-xl font-bold mb-4 text-slate-800">완벽한 개방성</h3>
                    <p class="text-slate-600 leading-relaxed text-sm">폐쇄적인 레거시 시스템을 벗어나세요. 라라벨 기반의 표준 코드로 자유로운 커스터마이징을 지원합니다.</p>
                </div>
                <div class="p-10 border-b-4 border-emerald-500 bg-slate-50 rounded-2xl">
                    <div class="text-4xl mb-6">📉</div>
                    <h3 class="text-xl font-bold mb-4 text-slate-800">비용의 파괴적 혁신</h3>
                    <p class="text-slate-600 leading-relaxed text-sm">불필요한 세팅비와 고액 유지보수료를 걷어냈습니다. 타사 대비 운영비를 최대 90%까지 절감하세요.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="why" class="py-24 px-8 bg-slate-900 text-white">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-3xl md:text-4xl font-black mb-20 text-center">왜 LaraPress인가요?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-16">
                <div class="space-y-4">
                    <span class="text-indigo-400 font-black text-5xl block">01</span>
                    <h4 class="text-xl font-bold">SEO 최적화 아키텍처</h4>
                    <p class="text-slate-400 text-sm leading-relaxed">구글 검색 엔진에 최적화된 마크업과 속도로 기사의 도달 범위를 극대화합니다.</p>
                </div>
                <div class="space-y-4">
                    <span class="text-indigo-400 font-black text-5xl block">02</span>
                    <h4 class="text-xl font-bold">포털 송출 API 패키지</h4>
                    <p class="text-slate-400 text-sm leading-relaxed">네이버, 카카오 뉴스 송출 규격을 완벽 지원하는 API 모듈을 합리적인 가격에 제공합니다.</p>
                </div>
                <div class="space-y-4">
                    <span class="text-indigo-400 font-black text-5xl block">03</span>
                    <h4 class="text-xl font-bold">지능형 편집국 시스템</h4>
                    <p class="text-slate-400 text-sm leading-relaxed">AI 기사 요약 및 태그 자동 생성 기능으로 편집 업무의 효율을 혁신적으로 높입니다.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="pricing" class="py-24 px-8 bg-slate-50">
        <div class="max-w-[1400px] mx-auto">
            <h2 class="text-3xl md:text-4xl font-black text-center mb-20 text-indigo-900 tracking-tight">이용 플랜</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto">
                <div class="bg-white p-12 rounded-3xl shadow-sm border border-slate-200">
                    <h3 class="text-lg font-bold mb-4 text-slate-400 uppercase tracking-widest">Community Core</h3>
                    <div class="text-5xl font-black mb-8 text-slate-800">0원<span class="text-lg font-normal text-slate-400 ml-1">/평생</span></div>
                    <ul class="space-y-4 mb-12 text-slate-600 text-base">
                        <li>✅ CMS 엔진 평생 무료 제공</li>
                        <li>✅ 커뮤니티 게시판 무제한</li>
                        <li>✅ 기본 반응형 테마 패키지</li>
                        <li>✅ 오픈소스(MIT) 기반 라이선스</li>
                    </ul>
                    <button class="w-full py-4 border-2 border-indigo-600 text-indigo-600 rounded-xl font-bold hover:bg-indigo-50 transition">지금 무료로 설치하기</button>
                </div>
                <div class="bg-white p-12 rounded-3xl shadow-2xl border-2 border-indigo-600 relative overflow-hidden">
                    <div class="absolute top-0 right-0 bg-indigo-600 text-white px-6 py-1 text-xs font-bold uppercase tracking-widest">추천</div>
                    <h3 class="text-lg font-bold mb-4 text-indigo-600 uppercase tracking-widest">Media Pro</h3>
                    <div class="text-5xl font-black mb-8 text-slate-800">19,000원<span class="text-lg font-normal text-slate-400 ml-1">/월</span></div>
                    <ul class="space-y-4 mb-12 text-slate-600 font-medium text-base">
                        <li>✅ 네이버/카카오 뉴스 송출 API</li>
                        <li>✅ AI 기사 자동 요약 도구 연동</li>
                        <li>✅ 뉴스레터 발송 시스템 커넥터</li>
                        <li>✅ 프리미엄 언론 전용 테마</li>
                    </ul>
                    <button class="w-full py-4 bg-indigo-600 text-white rounded-xl font-bold shadow-lg hover:bg-indigo-700 transition">Pro 플랜 시작하기</button>
                </div>
            </div>
        </div>
    </section>

    <section id="contact" class="py-24 px-8 bg-white">
        <div class="max-w-6xl mx-auto bg-slate-900 rounded-[2.5rem] p-12 md:p-16 text-white shadow-2xl">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div>
                    <h2 class="text-3xl font-black mb-6 uppercase tracking-tight">Contact</h2>
                    <p class="text-slate-400 text-base mb-10 leading-relaxed font-light">
                        당신의 미디어가 성공할 수 있는 기술 파트너가 되겠습니다.<br>문의를 남겨주시면 24시간 이내에 개발자가 직접 답변 드립니다.
                    </p>
                    <div class="space-y-6 text-base font-medium">
                        <div class="flex items-center gap-4">
                            <span class="w-12 h-12 bg-indigo-600/20 text-indigo-400 rounded-xl flex items-center justify-center border border-indigo-500/20">📧</span>
                            support@larapress.io
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="w-12 h-12 bg-indigo-600/20 text-indigo-400 rounded-xl flex items-center justify-center border border-indigo-500/20">K</span>
                            카카오톡 ID: larapress
                        </div>
                    </div>
                </div>
                <form class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <input type="text" placeholder="성함" class="w-full bg-slate-800 border-none rounded-xl p-4 focus:ring-2 focus:ring-indigo-500 text-white outline-none">
                        <input type="email" placeholder="이메일" class="w-full bg-slate-800 border-none rounded-xl p-4 focus:ring-2 focus:ring-indigo-500 text-white outline-none">
                    </div>
                    <textarea placeholder="문의 내용을 입력하세요" rows="4" class="w-full bg-slate-800 border-none rounded-xl p-4 focus:ring-2 focus:ring-indigo-500 text-white outline-none"></textarea>
                    <button class="w-full py-4 bg-indigo-600 text-white rounded-xl font-bold text-lg hover:bg-indigo-700 transition shadow-xl shadow-indigo-900">문의 보내기</button>
                </form>
            </div>
        </div>
    </section>

    <footer class="py-16 border-t border-slate-200 text-center text-slate-400 text-xs">
        <p class="mb-2 font-black text-indigo-600 text-sm tracking-tighter">LARAPRESS</p>
        <p>&copy; 2026 LaraPress Media Solution. All rights reserved.</p>
    </footer>

</body>
</html>