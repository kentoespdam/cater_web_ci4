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

    public function getOriginal(): string
    {
        return $this->getOriginalBase64();
    }

    private function resolveImagePath(): string
    {
        $configFotoPath = env('app.fotoWmPath') ?: (config('App')->fotoWmPath ?? ROOTPATH . 'fotowm/');
        $basePath = rtrim($configFotoPath, '/') . '/';

        $cleanRelPath = ltrim(str_replace('|', '/', $this->pathFromDb), '/');
        $targetPath = $basePath . $cleanRelPath;

        if (is_file($targetPath)) {
            return $targetPath;
        }

        $fallbackPath = $basePath . 'notFound.png';

        if (is_file($fallbackPath)) {
            return $fallbackPath;
        }

        return '';
    }

    private function getThumbnailBase64(): string
    {
        try {
            $resolvedPath = $this->resolveImagePath();

            if ($resolvedPath === '') {
                return '';
            }

            $imageData = file_get_contents($resolvedPath);

            if ($imageData === false) {
                return '';
            }

            $sourceImage = imagecreatefromstring($imageData);

            if ($sourceImage === false) {
                return '';
            }

            $srcW = imagesx($sourceImage);
            $srcH = imagesy($sourceImage);
            $thumbW = (int) ($srcW * 0.5);
            $thumbH = (int) ($srcH * 0.5);

            $thumbnail = imagecreatetruecolor($thumbW, $thumbH);
            imagecopyresampled($thumbnail, $sourceImage, 0, 0, 0, 0, $thumbW, $thumbH, $srcW, $srcH);

            ob_start();
            imagejpeg($thumbnail);
            $thumbnailData = ob_get_clean();

            imagedestroy($sourceImage);
            imagedestroy($thumbnail);

            return base64_encode($thumbnailData);
        } catch (\Throwable $e) {
            return '';
        }
    }

    private function getOriginalBase64(): string
    {
        try {
            $resolvedPath = $this->resolveImagePath();

            if ($resolvedPath === '') {
                return '';
            }

            $imageData = file_get_contents($resolvedPath);

            if ($imageData === false) {
                return '';
            }

            return base64_encode($imageData);
        } catch (\Throwable $e) {
            return '';
        }
    }
}
