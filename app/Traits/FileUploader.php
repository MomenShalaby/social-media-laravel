<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait FileUploader
{

    /**
     * For Upload Images.
     * @param mixed $request
     * @param mixed $data
     * @param mixed $name
     * @param mixed|null $inputName
     * @return bool|string
     */
    public function uploadImage($request, $data, $name, $inputName = 'image')
    {
        $requestFile = $request->file($inputName);
        try {
            $dir = 'public/images/' . $name . '/image/';
            $fixName = $data->id . '-' . $name . '-' . uniqid() . '.' . $requestFile->extension();

            if ($requestFile) {
                Storage::putFileAs($dir, $requestFile, $fixName);
                $request->image = '/storage/images/' . $name . '/image/' . $fixName;

                $data->update([
                    $inputName => $request->image,
                ]);
            }

            return true;
        } catch (\Throwable $th) {
            report($th);

            return $th->getMessage();
        }
    }
    /**
     * Delete an image file.
     *
     * @param string $fileName
     * @param string $directory
     * @return bool|string
     */
    public function deleteImage($imageUrl)
    {
        try {
            // Extract the file path from the URL
            $filePath = str_replace('/storage', 'public', $imageUrl);

            // Check if the file exists

            if (Storage::exists($filePath)) {
                // Delete the file
                Storage::delete($filePath);

                return true; // Image deleted successfully
            }

            return false; // Image file does not exist
        } catch (\Throwable $th) {
            report($th);

            return $th->getMessage(); // Error occurred while deleting the image
        }
    }

}