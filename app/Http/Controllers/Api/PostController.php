<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Traits\FileUploader;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

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
            'user_id' => 1,
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

        $validatedData = $request->validate([
            'content' => 'sometimes|string|max:255',
        ]);
        $post->update($validatedData);
        $this->uploadImage($request, $post, "post-image");
        $this->deleteImage($post->image);

        return $this->success($post, "data updated", 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $this->deleteImage($post->image);
        $post->delete();
        return response(status: 204);
    }
}
