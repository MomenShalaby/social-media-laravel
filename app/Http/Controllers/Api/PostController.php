<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Policies\PostPolicy;
use App\Traits\FileUploader;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    use HttpResponses;
    use FileUploader;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $posts = PostResource::collection(Post::with('user')->paginate());
        return $this->success($posts, "data is here", 200, true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'content' => 'required|max:255',
            'image' => 'nullable|image:jpeg,png,jpg'
        ]);

        $post = Post::create([
            'content' => $validatedData['content'],
            'user_id' => $request->user()->id,
        ]);

        $this->uploadImage($request, $post, "post-image");
        $post = new PostResource($post);
        return $this->success($post, "data inserted", 201);
    }



    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {

        $post->load('user');
        $post = new PostResource($post);
        return $this->success($post, "data is here", 200);


    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        // if ($request->user()->id !== $post->user_id) {
        //     return $this->error("Unauthorized", 401);
        // }
        Gate::authorize('update', $post);
        $validatedData = $request->validate([
            'content' => 'sometimes|string|max:255',
            'image' => 'sometimes|image:jpeg,png,jpg'
        ]);
        $post->update($validatedData);
        $this->deleteImage($post->image);
        $this->uploadImage($request, $post, "post-image");

        return $this->success($post, "data updated", 201);
    }

    public function updatePostImage(Request $request, Post $post)
    {

        Gate::authorize('updatePostImage', $post);

        $validatedData = $request->validate([
            'image' => 'sometimes|image:jpeg,png,jpg'
        ]);

        $this->deleteImage($post->image);
        $this->uploadImage($request, $post, 'post-image');

        $post = new PostResource($post);
        return $this->success($post, 'updated', 202);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Post $post)
    {
        Gate::authorize('delete', $post);

        $this->deleteImage($post->image);
        $post->delete();
        return response(status: 204);
    }
}
