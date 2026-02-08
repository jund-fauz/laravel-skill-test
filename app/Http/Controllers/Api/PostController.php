<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->has('page')) {
            $posts = Post::with('user')
                ->orderBy('published_at', 'desc')
                ->paginate(20, ['*'], 'page', $request->page)
                ->where('is_draft', '=', 0)
                ->where('published_at', '<', now())
                ->toArray();
        } else {
            $posts = Post::with('user')
                ->limit(20)
                ->where('is_draft', '=', 0)
                ->where('published_at', '<', now())->get();
        }

        return (new PostResource($posts))
            ->additional(['message' => 'Posts retrieved successfully.'])
            ->response();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'string|required|max:255|min:3',
            'content' => 'string|required|min:3',
            'is_draft' => 'boolean|sometimes',
            'published_at' => 'date|sometimes',
        ]);

        $post = Post::create([...$validated, 'user_id' => auth()->id()]);

        return (new PostResource($post))
            ->additional(['message' => 'Posts created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        if ($post->is_draft || $post->published_at > now()) {
            return response()->json(['message' => 'Post not found.'], 404);
        }

        return (new PostResource($post))
            ->additional(['message' => 'Post retrieved successfully'])
            ->response();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        if ($post->user_id != auth()->id()) {
            return response()->json(['message' => "You cannot edit other person's post"], 403);
        }

        $validated = $request->validate([
            'title' => 'string|sometimes|max:255|min:3',
            'content' => 'string|sometimes|min:3',
            'is_draft' => 'boolean|sometimes',
            'published_at' => 'date|sometimes',
        ]);

        $post->update($validated);

        return (new PostResource($post))
            ->additional(['message' => 'Post updated successfully'])
            ->response();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        if ($post->user_id != auth()->id()) {
            return response()->json(['message' => "You cannot delete other person's post"], 403);
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully'], 200);
    }

    public function create(Post $post)
    {
        if ($post->user_id != auth()->id()) {
            return response()->json(['message' => "You cannot edit other person's post"], 403);
        }

        return 'posts.create';
    }

    public function edit(Post $post)
    {
        if ($post->user_id != auth()->id()) {
            return response()->json(['message' => "You cannot delete other person's post"], 403);
        }

        return 'posts.edit';
    }
}
