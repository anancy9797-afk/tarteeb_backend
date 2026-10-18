<?php


namespace Modules\Common\Helpers;

use Intervention\Image\Laravel\Facades\Image;

trait UploadHelper
{
    public function upload($imageFromRequest, $imageFolder, $resize = false)
    {
        if (!file_exists(public_path('uploads/'.$imageFolder))) {
            mkdir(public_path('uploads/'.$imageFolder), 0777, true);
        }

        $fileName = time() . $imageFromRequest->getClientOriginalName();
        $location = public_path('uploads/' . $imageFolder . '/' . $fileName);

        $image = Image::read($imageFromRequest);
        if ($resize == true) {

            $image->resize(500, 500);
        }
        $image->save($location, 50);

        # Optional Resize.
        // if ($resize == true) {
        //     $image->resize(100, 70);
        //     $newlocation = public_path('uploads/' . $imageFolder . '/thumb' . '/' . $fileName);
        //     $image->save($newlocation, 40);
        // }


        return $fileName;
    }

    public function uploadFile($fileFromRequest, $fileFolder)
    {
        $fileName = time() . '.' . $fileFromRequest->getClientOriginalName();
        $location = public_path('uploads/' . $fileFolder . '/');
        $fileFromRequest->move($location, $fileName);

        return $fileName;
    }

    public function getImageName($folderName, $imagePath)
    {
        $needle = $folderName . '/';
        return substr($imagePath, strpos($imagePath, $needle) + strlen($needle));
    }
}
