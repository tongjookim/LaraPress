<?php $__env->startSection('title', '알림판'); ?>

<?php $__env->startSection('admin-content'); ?>
<h1 class="wp-page-title">알림판</h1>


<div class="admin-grid-4">
    <div class="wp-widget">
        <div class="wp-widget-body" style="display:flex;align-items:center;gap:14px;">
            <div style="width:48px;height:48px;border-radius:50%;background:#2271b1;display:flex;align-items:center;justify-content:center;">
                <svg width="22" height="22" fill="none" stroke="#fff" viewBox="0 0 24 24" stroke-width="2"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <div style="font-size:28px;font-weight:900;color:#1d2327;line-height:1;"><?php echo e(number_format($stats['posts'])); ?></div>
                <div style="font-size:12px;color:#646970;margin-top:2px;">게시글</div>
            </div>
        </div>
    </div>

    <div class="wp-widget">
        <div class="wp-widget-body" style="display:flex;align-items:center;gap:14px;">
            <div style="width:48px;height:48px;border-radius:50%;background:#00a32a;display:flex;align-items:center;justify-content:center;">
                <svg width="22" height="22" fill="none" stroke="#fff" viewBox="0 0 24 24" stroke-width="2"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            </div>
            <div>
                <div style="font-size:28px;font-weight:900;color:#1d2327;line-height:1;"><?php echo e(number_format($stats['comments'])); ?></div>
                <div style="font-size:12px;color:#646970;margin-top:2px;">댓글</div>
            </div>
        </div>
    </div>

    <div class="wp-widget">
        <div class="wp-widget-body" style="display:flex;align-items:center;gap:14px;">
            <div style="width:48px;height:48px;border-radius:50%;background:#8c5ae3;display:flex;align-items:center;justify-content:center;">
                <svg width="22" height="22" fill="none" stroke="#fff" viewBox="0 0 24 24" stroke-width="2"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <div>
                <div style="font-size:28px;font-weight:900;color:#1d2327;line-height:1;"><?php echo e(number_format($stats['users'])); ?></div>
                <div style="font-size:12px;color:#646970;margin-top:2px;">회원</div>
            </div>
        </div>
    </div>

    <div class="wp-widget">
        <div class="wp-widget-body" style="display:flex;align-items:center;gap:14px;">
            <div style="width:48px;height:48px;border-radius:50%;background:#dba617;display:flex;align-items:center;justify-content:center;">
                <svg width="22" height="22" fill="none" stroke="#fff" viewBox="0 0 24 24" stroke-width="2"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </div>
            <div>
                <div style="font-size:28px;font-weight:900;color:#1d2327;line-height:1;"><?php echo e(number_format($stats['total_views'])); ?></div>
                <div style="font-size:12px;color:#646970;margin-top:2px;">총 조회수</div>
            </div>
        </div>
    </div>
</div>


<div class="admin-grid-2">

    
    <div>
        
        <div class="wp-widget" style="margin-bottom:16px;">
            <div class="wp-widget-header">📊 사이트 현황</div>
            <div class="wp-widget-body">
                <table style="width:100%;font-size:13px;">
                    <tr style="border-bottom:1px solid #f0f0f1;">
                        <td style="padding:8px 0;color:#646970;">게시판</td>
                        <td style="padding:8px 0;text-align:right;font-weight:700;"><?php echo e($stats['boards']); ?>개</td>
                    </tr>
                    <tr style="border-bottom:1px solid #f0f0f1;">
                        <td style="padding:8px 0;color:#646970;">게시글</td>
                        <td style="padding:8px 0;text-align:right;font-weight:700;"><?php echo e(number_format($stats['posts'])); ?>개</td>
                    </tr>
                    <tr style="border-bottom:1px solid #f0f0f1;">
                        <td style="padding:8px 0;color:#646970;">댓글</td>
                        <td style="padding:8px 0;text-align:right;font-weight:700;"><?php echo e(number_format($stats['comments'])); ?>개</td>
                    </tr>
                    <tr style="border-bottom:1px solid #f0f0f1;">
                        <td style="padding:8px 0;color:#646970;">회원</td>
                        <td style="padding:8px 0;text-align:right;font-weight:700;"><?php echo e(number_format($stats['users'])); ?>명</td>
                    </tr>
                    <tr style="border-bottom:1px solid #f0f0f1;">
                        <td style="padding:8px 0;color:#646970;">기사 (전체)</td>
                        <td style="padding:8px 0;text-align:right;font-weight:700;"><?php echo e(number_format($stats['articles'])); ?>개</td>
                    </tr>
                    <tr style="border-bottom:1px solid #f0f0f1;">
                        <td style="padding:8px 0;color:#646970;">기사 (발행)</td>
                        <td style="padding:8px 0;text-align:right;font-weight:700;"><?php echo e(number_format($stats['articles_published'])); ?>개</td>
                    </tr>
                    <tr style="border-bottom:1px solid #f0f0f1;">
                        <td style="padding:8px 0;color:#646970;">페이지</td>
                        <td style="padding:8px 0;text-align:right;font-weight:700;"><?php echo e($stats['pages']); ?>개</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0;color:#646970;">총 조회수</td>
                        <td style="padding:8px 0;text-align:right;font-weight:700;"><?php echo e(number_format($stats['total_views'])); ?>회</td>
                    </tr>
                </table>
            </div>
        </div>

        
        <div class="wp-widget" style="margin-bottom:16px;">
            <div class="wp-widget-header">📋 게시판별 현황</div>
            <div class="wp-widget-body" style="padding:0;">
                <table class="wp-list-table">
                    <thead>
                        <tr>
                            <th>게시판</th>
                            <th style="text-align:right;">게시글</th>
                            <th style="text-align:right;">조회수</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $boardStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $board): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <a href="<?php echo e(route('bbs.index', $board->board_id)); ?>" style="color:#2271b1;text-decoration:none;"><?php echo e($board->board_name); ?></a>
                            </td>
                            <td style="text-align:right;"><?php echo e(number_format($board->posts_count)); ?></td>
                            <td style="text-align:right;"><?php echo e(number_format($board->posts_sum_view_count ?? 0)); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    
    <div>
        
        <div class="wp-widget" style="margin-bottom:16px;">
            <div class="wp-widget-header" style="display:flex;justify-content:space-between;align-items:center;">
                <span>📰 최근 기사</span>
                <a href="<?php echo e(route('admin.articles')); ?>" style="font-size:12px;font-weight:400;color:#2271b1;text-decoration:none;">전체 보기 →</a>
            </div>
            <div class="wp-widget-body" style="padding:0;">
                <?php $__empty_1 = true; $__currentLoopData = $recentArticles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div style="padding:10px 14px;border-bottom:1px solid #f0f0f1;font-size:13px;display:flex;justify-content:space-between;align-items:flex-start;gap:12px;">
                    <div style="flex:1;min-width:0;">
                        <a href="<?php echo e(route('admin.article.edit', $article->id)); ?>" style="color:#2271b1;text-decoration:none;font-weight:500;display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                            <?php echo e($article->title); ?>

                        </a>
                        <div style="font-size:11px;color:#8c8f94;margin-top:3px;display:flex;align-items:center;gap:6px;">
                            <span><?php echo e($article->user->name ?? '알 수 없음'); ?></span>
                            <?php if($article->category): ?>
                            <span>· <?php echo e($article->category->name); ?></span>
                            <?php endif; ?>
                            <span style="display:inline-block;padding:0 5px;border-radius:2px;font-size:10px;font-weight:600;
                                <?php echo e($article->status === 'published' ? 'background:#dcfce7;color:#166534;' : ($article->status === 'draft' ? 'background:#f3f4f6;color:#374151;' : 'background:#fef9c3;color:#854d0e;')); ?>">
                                <?php echo e($article->status === 'published' ? '발행' : ($article->status === 'draft' ? '임시저장' : $article->status)); ?>

                            </span>
                        </div>
                    </div>
                    <span style="font-size:11px;color:#8c8f94;white-space:nowrap;"><?php echo e($article->created_at->format('m.d H:i')); ?></span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div style="padding:24px;text-align:center;color:#8c8f94;font-size:13px;">기사가 없습니다.</div>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="wp-widget" style="margin-bottom:16px;">
            <div class="wp-widget-header">🕐 최근 게시글</div>
            <div class="wp-widget-body" style="padding:0;">
                <?php $__empty_1 = true; $__currentLoopData = $recentPosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div style="padding:10px 14px;border-bottom:1px solid #f0f0f1;font-size:13px;display:flex;justify-content:space-between;align-items:flex-start;gap:12px;">
                    <div style="flex:1;min-width:0;">
                        <a href="<?php echo e(route('bbs.show', [$post->board->board_id ?? 'free', $post->id])); ?>" style="color:#2271b1;text-decoration:none;font-weight:500;display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                            <?php echo e($post->title); ?>

                        </a>
                        <div style="font-size:11px;color:#8c8f94;margin-top:3px;">
                            <?php echo e($post->user->name ?? '알 수 없음'); ?> · <?php echo e($post->board->board_name ?? ''); ?>

                        </div>
                    </div>
                    <span style="font-size:11px;color:#8c8f94;white-space:nowrap;"><?php echo e($post->created_at->format('m.d H:i')); ?></span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div style="padding:24px;text-align:center;color:#8c8f94;font-size:13px;">게시글이 없습니다.</div>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="wp-widget" style="margin-bottom:16px;">
            <div class="wp-widget-header">💬 최근 댓글</div>
            <div class="wp-widget-body" style="padding:0;">
                <?php $__empty_1 = true; $__currentLoopData = $recentComments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div style="padding:10px 14px;border-bottom:1px solid #f0f0f1;font-size:13px;">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                        <div style="width:28px;height:28px;border-radius:50%;background:#ddd;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#666;flex-shrink:0;">
                            <?php echo e(mb_substr($comment->user->name ?? '?', 0, 1)); ?>

                        </div>
                        <div>
                            <span style="font-weight:600;color:#1d2327;"><?php echo e($comment->user->name ?? '알 수 없음'); ?></span>
                            <span style="color:#8c8f94;font-size:11px;margin-left:6px;"><?php echo e($comment->created_at->format('m.d H:i')); ?></span>
                        </div>
                    </div>
                    <div style="color:#646970;font-size:12px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;padding-left:36px;">
                        <?php echo e(Str::limit($comment->content, 80)); ?>

                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div style="padding:24px;text-align:center;color:#8c8f94;font-size:13px;">댓글이 없습니다.</div>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="wp-widget">
            <div class="wp-widget-header">👤 최근 가입 회원</div>
            <div class="wp-widget-body" style="padding:0;">
                <?php $__currentLoopData = $recentUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="padding:8px 14px;border-bottom:1px solid #f0f0f1;font-size:13px;display:flex;justify-content:space-between;align-items:center;">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <div style="width:28px;height:28px;border-radius:50%;background:<?php echo e($user->is_admin ? '#dbeafe' : '#f3f4f6'); ?>;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:<?php echo e($user->is_admin ? '#2271b1' : '#666'); ?>;">
                            <?php echo e(mb_substr($user->name, 0, 1)); ?>

                        </div>
                        <div>
                            <span style="font-weight:500;"><?php echo e($user->name); ?></span>
                            <?php if($user->is_admin): ?>
                                <span class="wp-badge wp-badge-admin" style="margin-left:4px;">관리자</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <span style="font-size:11px;color:#8c8f94;"><?php echo e($user->created_at->format('Y.m.d')); ?></span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/laraboard/www/resources/views/admin/dashboard.blade.php ENDPATH**/ ?>