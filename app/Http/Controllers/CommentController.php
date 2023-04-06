<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Post;



class CommentController extends Controller {
    public function index($id) {
        $post = Post::findOrFail($id);
        $comments = $post->comments;

        return response()->json([
            'comments' => $comments,
        ]);
    }

    public function store($id, Request $request) {
        $user = $request->user();
        $post = Post::findOrFail($id);

        $this->validate($request, [
            'content' => 'required|string|max:255',
        ]);

    $comment = new Comment([
        'content' => $request->input('content'),
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);

    $comment->save();

    return response()->json([
        'message' => 'Comment added successfully',
        'comment' => $comment,
    ]);
}

}
