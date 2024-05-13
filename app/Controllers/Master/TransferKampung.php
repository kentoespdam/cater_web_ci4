<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\Sikompak\PegawaiModel;
use App\Models\UserAndroidModel;
use CodeIgniter\HTTP\ResponseInterface;

class TransferKampung extends BaseController
{
    public function index()
    {
        helper('bulan');
        $view = \Config\Services::renderer();
        $session = session()->get("logged_in");
        if ($session['kdStatus'] !== "admin")
            return redirect()->to('/');

        $userModel = new UserAndroidModel();
        $userList = $userModel->select("username")->findAll();

        $pegawaiModel = new PegawaiModel();
        $kampungList = $pegawaiModel->select("NIK AS id, nama")->findAll();

        return $view->setVar("session", $session)
            ->setVar("userList", $userList)
            ->setVar("kampungList", $kampungList)
            ->render('master/transfer_kampung');
    }
}
