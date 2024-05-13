<?php

namespace App\Controllers\Api\Master;

use App\Controllers\BaseController;
use App\Models\Sikompak\PegawaiModel;
use App\Models\UserAndroidModel;
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
        $sort = $req->sort ?? "user";
        $order = $req->order ?? "ASC";

        return $this->respond([
            "total" => $this->getTotal(),
            "rows" => $this->getListUser($page, $size, $sort, $order)
        ]);
    }

    public function kampung(string $user)
    {
        return $this->respond($this->findKampungByUser($user));
    }

    private function getListUser(int $page, int $size, string $sort, string $order): array
    {
        $offset = ($page - 1) * $size;

        $userModel = new UserAndroidModel();
        return $userModel->select("
                    uid, 
                    username AS user,
                    nama_lengkap AS nama,
                    email,
                    cabang AS cabang
                ")
            ->orderBy($sort, $order)
            ->findAll($size, $offset);
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

    public function save()
    {
        $input = $this->request->getPost();

        $rules = [
            'user' => 'required',
            'nama' => 'required',
            'cabang' => 'required',
        ];

        $errors = [
            'user' => [
                'required' => 'User harus diisi!',
            ],
            'nama' => [
                'required' => 'Nama harus diisi!',
            ], 'email' => [
                'required' => 'Email harus diisi!',
                'valid_email' => 'Email tidak valid!'
            ],
            'cabang' => [
                'required' => 'Cabang harus diisi!',
            ],
        ];

        $validation = \Config\Services::validation();
        $validation->setRules($rules, $errors);

        if (!$validation->run($input)) {
            return $this->fail($validation->getErrors());
        }

        $resp = $this->saveUser((object)$input);

        return $this->respondCreated($resp);
    }

    private function saveUser(object $input)
    {
        $userModel = new UserAndroidModel();
        $exist = $userModel->where("email", $input->email)->first();
        if ($exist)
            return [
                'error' => 400,
                'status' => 400,
                'message' => 'Email sudah terdaftar'
            ];

        $data = [
            "unique_id" => uniqid(),
            "username" => $input->user,
            "nama_lengkap" => $input->nama,
            "cabang" => $input->cabang,
            "email" => $input->email,
            "password" => "tanggal25"
        ];

        $userModel->insert($data);
        return [
            'status' => 201,
            'message' => 'User created',
        ];
    }

    public function delete($user)
    {
        $userModel = new UserAndroidModel();
        $rows = $userModel->join("pegawai", "users.username=pegawai.pembaca_meter")
            ->where("users.username", $user)
            ->findAll();

        if (count($rows) > 0)
            return $this->respond([
                "error" => 500,
                "status" => 500,
                "messages" => 'Tidak dapat menghapus User, User masih memiliki kampung!'
            ])->setStatusCode(500);

        $userModel->where("username", $user)->delete();
        return $this->respondDeleted([
            "messages" => 'User deleted'
        ]);
    }
}
