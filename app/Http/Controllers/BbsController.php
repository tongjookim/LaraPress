<?php
// app/Http/Controllers/BbsController.php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BbsController extends Controller
{
    // 게시판 목록
    public function index(Request $request, $boardId)
    {
        $board = Board::where('board_id', $boardId)->firstOrFail();
        
        $query = Post::where('board_id', $board->id)->with('user')->withCount('comments');
        
        // 검색 기능
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }
        
        $posts = $query->orderBy('is_notice', 'desc')
                       ->orderBy('created_at', 'desc')
                       ->paginate($board->posts_per_page);

        return view("skin.board.{$board->skin}.list", compact('board', 'posts'));
    }

    // 글 보기
    public function show($boardId, $postId)
    {
        $board = Board::where('board_id', $boardId)->firstOrFail();
        $post = Post::with(['user', 'comments.user'])->findOrFail($postId);
        
        // 조회수 증가
        $post->incrementViewCount();

        return view("skin.board.{$board->skin}.view", compact('board', 'post'));
    }

    // 글쓰기 폼
    public function create($boardId)
    {
        $board = Board::where('board_id', $boardId)->firstOrFail();
        return view("skin.board.{$board->skin}.write", compact('board'));
    }

    // 글 저장
    public function store(Request $request, $boardId)
    {
        $board = Board::where('board_id', $boardId)->firstOrFail();
        
        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
        ]);

        Post::create([
            'board_id' => $board->id,
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'content' => $validated['content'],
        ]);

        return redirect()->route('bbs.index', $boardId)
            ->with('success', '글이 등록되었습니다.');
    }

    // 글 수정 폼
    public function edit($boardId, $postId)
    {
        $board = Board::where('board_id', $boardId)->firstOrFail();
        $post = Post::findOrFail($postId);

        // 권한 체크
        if ($post->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        return view("skin.board.{$board->skin}.edit", compact('board', 'post'));
    }

    // 글 업데이트
    public function update(Request $request, $boardId, $postId)
    {
        $post = Post::findOrFail($postId);

        // 권한 체크
        if ($post->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
        ]);

        $post->update($validated);

        return redirect()->route('bbs.show', [$boardId, $postId])
            ->with('success', '글이 수정되었습니다.');
    }

    // 댓글 작성
    public function storeComment(Request $request, $boardId, $postId)
    {
        $validated = $request->validate([
            'content' => 'required',
        ]);

        Comment::create([
            'post_id' => $postId,
            'user_id' => Auth::id(),
            'content' => $validated['content'],
        ]);

        return back()->with('success', '댓글이 등록되었습니다.');
    }

    // 글 삭제
    public function destroy($boardId, $postId)
    {
        $post = Post::findOrFail($postId);

        // 권한 체크
        if ($post->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $post->delete();

        return redirect()->route('bbs.index', $boardId)
            ->with('success', '글이 삭제되었습니다.');
    }

    // 댓글 삭제
    public function deleteComment($boardId, $postId, $commentId)
    {
        $comment = Comment::findOrFail($commentId);

        // 권한 체크
        if ($comment->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $comment->delete();

        return back()->with('success', '댓글이 삭제되었습니다.');
    }
}