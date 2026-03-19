<?php
// install/step4.php

if (!isset($_SESSION['db_host'])) {
    header('Location: ?step=1');
    exit;
}

$success = false;
$error = null;

try {
    // 1. .env 파일 생성 (기존 파일 삭제 후 재생성)
    $envPath = dirname(__DIR__) . '/.env';
    
    // 기존 .env 파일이 있으면 삭제
    if (file_exists($envPath)) {
        unlink($envPath);
    }
    
    $envContent = "APP_NAME=Laraboard
APP_ENV=production
APP_KEY=base64:" . base64_encode(random_bytes(32)) . "
APP_DEBUG=false
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST={$_SESSION['db_host']}
DB_PORT={$_SESSION['db_port']}
DB_DATABASE={$_SESSION['db_database']}
DB_USERNAME={$_SESSION['db_username']}
DB_PASSWORD={$_SESSION['db_password']}

BROADCAST_DRIVER=log
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
";
    
    if (file_put_contents($envPath, $envContent) === false) {
        throw new Exception('.env 파일을 생성할 수 없습니다. 권한을 확인하세요.');
    }
    
    // 2. DB 연결 및 테이블 생성
    $dsn = "mysql:host={$_SESSION['db_host']};port={$_SESSION['db_port']};dbname={$_SESSION['db_database']}";
    $pdo = new PDO($dsn, $_SESSION['db_username'], $_SESSION['db_password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Users 테이블
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) UNIQUE NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        name VARCHAR(255) NOT NULL,
        role ENUM('user', 'admin') DEFAULT 'user',
        is_active BOOLEAN DEFAULT 1,
        email_verified_at TIMESTAMP NULL,
        remember_token VARCHAR(100) NULL,
        created_at TIMESTAMP NULL,
        updated_at TIMESTAMP NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Boards 테이블
    $pdo->exec("CREATE TABLE IF NOT EXISTS boards (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        board_id VARCHAR(255) UNIQUE NOT NULL,
        board_name VARCHAR(255) NOT NULL,
        skin VARCHAR(255) DEFAULT 'basic',
        posts_per_page INT DEFAULT 20,
        use_comment BOOLEAN DEFAULT 1,
        is_active BOOLEAN DEFAULT 1,
        `order` INT DEFAULT 0,
        created_at TIMESTAMP NULL,
        updated_at TIMESTAMP NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Posts 테이블
    $pdo->exec("CREATE TABLE IF NOT EXISTS posts (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        board_id BIGINT UNSIGNED NOT NULL,
        user_id BIGINT UNSIGNED NOT NULL,
        title VARCHAR(255) NOT NULL,
        content TEXT NOT NULL,
        view_count INT DEFAULT 0,
        is_notice BOOLEAN DEFAULT 0,
        created_at TIMESTAMP NULL,
        updated_at TIMESTAMP NULL,
        FOREIGN KEY (board_id) REFERENCES boards(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_board_created (board_id, created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Comments 테이블
    $pdo->exec("CREATE TABLE IF NOT EXISTS comments (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        post_id BIGINT UNSIGNED NOT NULL,
        user_id BIGINT UNSIGNED NOT NULL,
        content TEXT NOT NULL,
        created_at TIMESTAMP NULL,
        updated_at TIMESTAMP NULL,
        FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_post (post_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Settings 테이블
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `key` VARCHAR(255) UNIQUE NOT NULL,
        value TEXT NULL,
        created_at TIMESTAMP NULL,
        updated_at TIMESTAMP NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // 3. 관리자 계정 생성 (중복 체크)
    $hashedPassword = password_hash($_SESSION['admin_password'], PASSWORD_DEFAULT);
    
    // 기존 관리자가 있는지 확인
    $checkAdmin = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
    $checkAdmin->execute([$_SESSION['admin_username'], $_SESSION['admin_email']]);
    
    if ($checkAdmin->fetchColumn() == 0) {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, name, role, created_at, updated_at) 
                              VALUES (?, ?, ?, ?, 'admin', NOW(), NOW())");
        $stmt->execute([
            $_SESSION['admin_username'],
            $_SESSION['admin_email'],
            $hashedPassword,
            $_SESSION['admin_name']
        ]);
    }
    
    // 4. 기본 설정 저장 (중복 체크)
    $checkSetting = $pdo->prepare("SELECT COUNT(*) FROM settings WHERE `key` = ?");
    $checkSetting->execute(['site_name']);
    
    if ($checkSetting->fetchColumn() == 0) {
        $stmt = $pdo->prepare("INSERT INTO settings (`key`, value, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
        $stmt->execute(['site_name', $_SESSION['site_name']]);
    }
    
    // 5. 기본 게시판 생성 (중복 체크)
    $boards = [
        ['notice', '공지사항'],
        ['free', '자유게시판']
    ];
    
    foreach ($boards as $board) {
        $checkBoard = $pdo->prepare("SELECT COUNT(*) FROM boards WHERE board_id = ?");
        $checkBoard->execute([$board[0]]);
        
        if ($checkBoard->fetchColumn() == 0) {
            $stmt = $pdo->prepare("INSERT INTO boards (board_id, board_name, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
            $stmt->execute($board);
        }
    }
    
    $success = true;
    
    // 세션 정리
    session_destroy();
    
} catch(Exception $e) {
    $error = $e->getMessage();
}
?>

<?php if($success): ?>
<div class="text-center py-8">
    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
    </div>
    
    <h2 class="text-3xl font-bold text-gray-900 mb-4">설치가 완료되었습니다!</h2>
    <p class="text-gray-600 mb-8">Laravel BBS가 성공적으로 설치되었습니다.</p>
    
    <div class="bg-blue-50 rounded-lg p-6 mb-8 text-left">
        <h3 class="font-semibold text-gray-900 mb-3">다음 단계:</h3>
        <ol class="list-decimal list-inside space-y-2 text-gray-700">
            <li><code class="bg-white px-2 py-1 rounded text-sm">install</code> 폴더를 삭제하세요 (보안)</li>
            <li><code class="bg-white px-2 py-1 rounded text-sm">composer dump-autoload</code> 명령을 실행하세요</li>
            <li><code class="bg-white px-2 py-1 rounded text-sm">php artisan config:cache</code> 명령을 실행하세요</li>
            <li>관리자 계정으로 로그인하여 사이트를 관리하세요</li>
        </ol>
    </div>
    
    <div class="space-x-4">
        <a href="../" class="inline-block px-8 py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-lg hover:shadow-lg transition font-medium">
            사이트로 이동 →
        </a>
        <a href="../admin" class="inline-block px-8 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
            관리자 페이지
        </a>
    </div>
</div>

<?php else: ?>
<div class="text-center py-8">
    <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </div>
    
    <h2 class="text-3xl font-bold text-gray-900 mb-4">설치 중 오류 발생</h2>
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded text-left mb-8">
        <p class="text-red-700"><?= htmlspecialchars($error) ?></p>
    </div>
    
    <a href="?step=1" class="inline-block px-8 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
        처음부터 다시 시작
    </a>
</div>
<?php endif; ?>
