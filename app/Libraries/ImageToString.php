<?php

namespace App\Libraries;

class ImageToString
{
    private string $pathFromDb;

    public function __construct(string $path)
    {
        $this->pathFromDb = $path;
    }

    public function get(): string
    {
        return $this->getThumbnailBase64();
    }

    private function getThumbnailBase64()
    {
        $filePath = str_replace("|", "/", $this->pathFromDb);
        $filePath = str_replace("//192.168.1.215", "192.168.1.215:5553", $filePath);
        $imageData = file_get_contents("http://$filePath");
        $sourceImage = imagecreatefromstring($imageData);
        $thumbnailDimensions = $this->calculateThumbnailDimensions(imagesx($sourceImage), imagesy($sourceImage));
        $thumbnail = $this->createThumbnail($sourceImage, $thumbnailDimensions['width'], $thumbnailDimensions['height']);
        $thumbnailData = $this->generateThumbnailData($thumbnail);
        $this->cleanUp($sourceImage, $thumbnail);

        return base64_encode($thumbnailData);
    }

    private function calculateThumbnailDimensions($sourceWidth, $sourceHeight)
    {
        $thumbnailWidth = $sourceWidth * 0.5;
        $thumbnailHeight = $sourceHeight * 0.5;

        return [
            'width' => $thumbnailWidth,
            'height' => $thumbnailHeight,
        ];
    }

    private function createThumbnail($sourceImage, $width, $height)
    {
        $thumbnail = imagecreatetruecolor($width, $height);
        imagecopyresized($thumbnail, $sourceImage, 0, 0, 0, 0, $width, $height, imagesx($sourceImage), imagesy($sourceImage));

        return $thumbnail;
    }

    private function generateThumbnailData($thumbnail)
    {
        ob_start();
        imagejpeg($thumbnail);
        $thumbnailData = ob_get_clean();

        return $thumbnailData;
    }

    private function cleanUp($sourceImage, $thumbnail)
    {
        imagedestroy($sourceImage);
        imagedestroy($thumbnail);
    }
}
