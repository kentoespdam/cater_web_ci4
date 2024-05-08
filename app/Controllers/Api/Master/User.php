<?php

namespace App\Controllers\Api\Master;

use App\Controllers\BaseController;
use App\Models\Sikompak\PegawaiModel;
use CodeIgniter\API\ResponseTrait;

class User extends BaseController
{
    use ResponseTrait;
    protected $type = "json";

    public function index()
    {
        $req = (object)$this->request->getGet();
        $page = $req->page ?? 1;
        $size = $req->rows ?? 10;

        return $this->respond([
            "total" => $this->getTotal(),
            "rows" => $this->getListUser($page, $size)
        ]);
    }

    public function kampung(string $user)
    {
        return $this->respond($this->findKampungByUser($user));
    }

    private function getListUser(int $page, int $size): array
    {
        $offset = ($page - 1) * $size;

        $pegawaiModel = new PegawaiModel();
        return $pegawaiModel->select("
                pembaca_meter AS user, 
                nama_lengkap AS nama, 
                wil AS wilayah, 
                cabang
            ")
            ->where("pembaca_meter IS NOT NULL")
            ->where("pembaca_meter !=", "-")
            ->groupBy("pembaca_meter")
            ->orderBy("wilayah", "ASC")
            ->orderBy("user", "ASC")
            ->limit($size, $offset)
            ->findAll();
    }

    private function getTotal(): int
    {
        $pegawaiModel = new PegawaiModel();
        $data = $pegawaiModel->distinct()
            ->select("pembaca_meter")
            ->where("pembaca_meter IS NOT NULL")
            ->where("pembaca_meter !=", "-")
            ->findAll();
        return count($data);
    }

    public function findKampungByUser(string $user)
    {
        $pegawaiModel = new PegawaiModel();
        $data = $pegawaiModel->select("nik AS id, nama AS kampung")
            ->where("pembaca_meter", $user)
            ->findAll();
        return $data;
    }
}
