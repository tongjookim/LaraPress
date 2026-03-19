<?php
// install/index.php
session_start();

// 재설치 모드 (reset 파라미터)
if (isset($_GET['reset'])) {
    // 세션 완전 초기화
    $_SESSION = array();
    session_destroy();
    session_start();
    
    // 캐시 파일 삭제 시도
    @unlink('../bootstrap/cache/config.php');
    @unlink('../bootstrap/cache/routes-v7.php');
    @unlink('../bootstrap/cache/services.php');
    
    header('Location: index.php?step=1');
    exit;
}

// 이미 설치되었는지 확인
if (file_exists('../.env') && !isset($_GET['force'])) {
    echo '<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>이미 설치됨</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-xl shadow-lg max-w-md">
        <h1 class="text-2xl font-bold text-gray-900 mb-4">이미 설치되었습니다</h1>
        <p class="text-gray-600 mb-6">Laravel BBS가 이미 설치되어 있습니다.</p>
        <div class="space-y-3">
            <a href="../" class="block w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-center">
                사이트로 이동
            </a>
            <a href="?reset=1" class="block w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-center">
                재설치하기 (주의: 모든 데이터 삭제)
            </a>
        </div>
        <p class="text-sm text-gray-500 mt-4">
            ※ 재설치 시 기존 데이터가 모두 삭제됩니다.
        </p>
    </div>
</body>
</html>';
    exit;
}

$step = $_GET['step'] ?? 1;
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel BBS 설치</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@400;500;700&display=swap');
        body { font-family: 'Noto+Sans+KR', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-purple-50 to-blue-50 min-h-screen py-12">
    <div class="max-w-3xl mx-auto px-4">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent mb-2">
                Laravel BBS 설치
            </h1>
            <p class="text-gray-600">단계별로 설치를 진행합니다</p>
        </div>

        <!-- Progress -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <?php for($i = 1; $i <= 4; $i++): ?>
                <div class="flex-1">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold <?= $step >= $i ? 'bg-purple-600 text-white' : 'bg-gray-300 text-gray-600' ?>">
                            <?= $i ?>
                        </div>
                        <?php if($i < 4): ?>
                        <div class="flex-1 h-1 mx-2 <?= $step > $i ? 'bg-purple-600' : 'bg-gray-300' ?>"></div>
                        <?php endif; ?>
                    </div>
                    <p class="text-xs text-gray-600 mt-2">
                        <?php
                        $steps = ['환경 확인', 'DB 설정', '관리자 설정', '완료'];
                        echo $steps[$i-1];
                        ?>
                    </p>
                </div>
                <?php endfor; ?>
            </div>
        </div>

        <!-- Content -->
        <div class="bg-white rounded-xl shadow-lg p-8">
            <?php
            switch($step) {
                case 1:
                    include 'step1.php';
                    break;
                case 2:
                    include 'step2.php';
                    break;
                case 3:
                    include 'step3.php';
                    break;
                case 4:
                    include 'step4.php';
                    break;
            }
            ?>
        </div>
    </div>
</body>
</html>