<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\VerifyEmailNotification;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification());
    }

    // role 계층: subscriber < author < editor < admin
    const ROLES = [
        'subscriber' => 0,
        'author'     => 1,
        'editor'     => 2,
        'admin'      => 3,
    ];

    const ROLE_LABELS = [
        'subscriber' => '구독자',
        'author'     => '작성자',
        'editor'     => '편집자',
        'admin'      => '관리자',
    ];

    protected $fillable = [
        'username', 'email', 'password', 'name', 'role', 'is_active',
        'bio', 'profile_image', 'author_box_enabled',
        'social_facebook', 'social_x', 'social_instagram', 'social_linkedin',
        'social_website', 'social_blog', 'social_pixabay', 'social_wikipedia', 'social_email',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at'   => 'datetime',
        'is_active'           => 'boolean',
        'author_box_enabled'  => 'boolean',
    ];

    // ── 관계 ──────────────────────────────────────────────

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function articleComments()
    {
        return $this->hasMany(ArticleComment::class);
    }

    // ── 역할 헬퍼 ─────────────────────────────────────────

    /** 역할 수준 반환 (subscriber=0 ~ admin=3) */
    public function roleLevel(): int
    {
        return self::ROLES[$this->role] ?? 0;
    }

    /** 최소 역할 이상인지 확인 */
    public function hasMinRole(string $minRole): bool
    {
        return $this->roleLevel() >= (self::ROLES[$minRole] ?? 99);
    }

    public function isAdmin(): bool     { return $this->role === 'admin'; }
    public function isEditor(): bool    { return $this->hasMinRole('editor'); }
    public function isAuthor(): bool    { return $this->hasMinRole('author'); }
    public function isSubscriber(): bool{ return $this->role === 'subscriber'; }

    /** 관리자 패널 접근 가능 여부 (author 이상) */
    public function canAccessAdmin(): bool { return $this->hasMinRole('author'); }

    /** 게시판 글쓰기 가능 여부 (author 이상) */
    public function canWriteBbs(): bool { return $this->hasMinRole('author'); }

    /** 기사 작성 가능 여부 */
    public function canWriteArticle(): bool { return $this->hasMinRole('author'); }

    /** 기사 승인/편집 가능 여부 */
    public function canApproveArticle(): bool { return $this->hasMinRole('editor'); }

    /** 역할 한국어 표시 */
    public function roleLabel(): string
    {
        return self::ROLE_LABELS[$this->role] ?? $this->role;
    }

    // 기존 $user->is_admin 프로퍼티 호환 액세서
    public function getIsAdminAttribute(): bool
    {
        return $this->role === 'admin';
    }
}
