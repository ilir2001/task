<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Facades\Validator;


class CommentController extends Controller {

    # index function which name dosen't matter we use for geting all comments for 1 post the post we get with id
    public function index($id) {
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['error' => 'Post with this id do not exist'], 404);
        }
        $comments = $post->comments;
        return response()->json([
            'comments' => $comments,
        ]);
    }

    # store function storing the comments for specific post also we get post with id as parmeter in url
    public function store($id, Request $request) {
        $user = $request->user();
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['error' => 'Post with this id do not exist'], 404);
        }

        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => "Write some comment in post"]);
        }
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
