<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;


class PostController extends Controller
{
    # index function i use for getting all post for anyone you dont need to have token for accessing posts
    public function index()
    {
        return Post::all();
    }

    # store token i use store to create posts but first you need to have a token because the post need user_id that present who made this post
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
    
    # show function uses for getting only one post aand is public you dont need a acces token to view specfic post
    public function show($id) {
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['error' => 'Post not found'], 404);
        }
        return response()->json(['post' => $post]);
    }

    # update function just like name help use update post but you can update only post you are owner, posts aren't yours you cannot update
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

    # destory function uses for deleting the post by id only post you are owner of them, other posts you cannot delete
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
