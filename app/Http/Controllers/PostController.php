<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;


class PostController extends Controller
{
    public function index()
    {
        return Post::all();
    }

    public function store(Request $request)
    {
        if(! auth()->check()) {
            return response()->json(['status' => 'error', 'message' => 'erro']);
        }
        try {  
            $user = auth()->user();
            $userID = $user->id;
            $user = User::find($userID);
            $post = new Post();
            $post->title = $request->title;
            $post->content = $request->content;
            $post->user_id = $userID;

            if ($post->save()) {
                return response()->json(['status' => 'success', 'message' => 'Post created successfully']);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'error' , 'message' => $e->getMessage()]);
        }
    }
    public function show($id) {
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['error' => 'Post not found'], 404);
        }
        return response()->json(['post' => $post]);
    }

    public function update(Request $request, $id) {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        if ($post->user_id !== auth()->id()) {
            return response()->json(['error' => 'You are not authorized to update this post'], 403);
        }

        $post->title = $request->input('title');
        $post->content = $request->input('content');
        $post->save();

        return response()->json(['post' => $post]);
    }

    public function destroy($id) {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        if ($post->user_id !== auth()->id()) {
            return response()->json(['error' => 'You are not authorized to delete this post'], 403);
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }
}
