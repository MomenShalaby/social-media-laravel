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

            return $request->image;
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




    /**
     * Upload an image file and update the image field.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param mixed $data
     * @param string $name
     * @param string $inputName
     * @return bool|string
     */
    public function updateImage($request, $data_to_add, $name, $data_to_delete, $inputName = 'image')
    {
        try {

            // Delete old image if it exists
            $this->deleteImage($data_to_delete->{$inputName});
            $path = $this->uploadImage($request, $data_to_add, $name);
            return $path;
        } catch (\Throwable $th) {
            report($th);

            return $th->getMessage(); // Return error message if an exception occurs
        }
    }
}