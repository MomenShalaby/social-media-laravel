<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProfileResource;
use App\Models\Profile;
use App\Traits\FileUploader;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    use HttpResponses;
    use FileUploader;

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {

        $profile = $request->user()->profile;
        $profile = new ProfileResource($profile);
        return $this->success($profile);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $profile = $request->user()->profile;

        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'bio' => 'sometimes|string|max:255',
        ]);

        $profile->update($validatedData);
        $request->user()->update($validatedData);


        $profile = new ProfileResource($profile);
        return $this->success($profile, 'updated', 202);
    }


    public function updateProfileImage(Request $request)
    {
        $profile = $request->user()->profile;

        $validatedData = $request->validate([
            'image' => 'sometimes|image:jpeg,png,jpg'
        ]);
        $this->deleteImage($profile->image);
        $this->uploadImage($request, $profile, 'profile-image');

        $profile = new ProfileResource($profile);
        return $this->success($profile, 'updated', 202);
    }
}
