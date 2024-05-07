<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\BacaMeterModel;

class CekFoto extends BaseController
{
    public function index()
    {
        $req = (object)request()->getGet();
        if (!isset($req->nosamw) || !isset($req->tglAwal) || !isset($req->tglAkhir) || empty($req->nosamw) || empty($req->tglAwal) || empty($req->tglAkhir))
            return response()->setJSON([]);
        $bacaMeterModel = new BacaMeterModel();
        $data = $bacaMeterModel->cekFoto($req->nosamw, $req->tglAwal, $req->tglAkhir);
        return response()->setJSON([
            "rows" => $data,
            "total" => count($data)
        ]);
    }

    public function detail($id)
    {
        $bacaMeterModel = new BacaMeterModel();
        $data = $bacaMeterModel
            ->select("no AS id, tgl, info,stan_kini, CONCAT(folderSS,'|',fileSS) AS foto")
            ->find($id);
        $data->foto = $this->getThumbnailBase64($data->foto);
        return response()->setJSON([$data]);
    }

    private function getThumbnailBase64($filePath)
    {
        $filePath = str_replace("|", "/", $filePath);
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
