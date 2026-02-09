<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
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
        $posts = Post::with('user')
            ->where('is_draft', false)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->paginate(20);

        return PostResource::collection($posts)
            ->additional(['message' => 'Posts retrieved successfully.'])
            ->response();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $post = Post::create([...$request->validated(), 'user_id' => $request->user()->id]);

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
        if ($post->is_draft || ($post->published_at && $post->published_at > now())) {
            abort(404, 'Post not found.');
        }

        $post->load('user');

        return (new PostResource($post))
            ->additional(['message' => 'Post retrieved successfully'])
            ->response();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        $post->update($request->validated());

        return (new PostResource($post))
            ->additional(['message' => 'Post updated successfully'])
            ->response();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully'], 200);
    }

    /**
     * Show the form for creating a new post.
     * Only authenticated users can access.
     */
    public function create()
    {
        return 'posts.create';
    }

    /**
     * Show the form for editing the specified post.
     * Only the post author can access.
     */
    public function edit(Post $post)
    {
        $this->authorize('update', $post);

        return 'posts.edit';
    }
}
