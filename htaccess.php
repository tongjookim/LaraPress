<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # 설치 폴더 접근 허용
    RewriteCond %{REQUEST_URI} ^/install/
    RewriteRule ^ - [L]

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# 보안 헤더
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-XSS-Protection "1; mode=block"
</IfModule>

# PHP 설정 (필요한 경우)
<IfModule mod_php.c>
    php_value upload_max_filesize 20M
    php_value post_max_size 20M
    php_value max_execution_time 300
    php_value max_input_time 300
</IfModule>

# 디렉토리 인덱싱 비활성화
Options -Indexes

# .env 파일 접근 차단
<Files .env>
    Order allow,deny
    Deny from all
</Files>

# 민감한 파일 접근 차단
<FilesMatch "^\.">
    Order allow,deny
    Deny from all
</FilesMatch>