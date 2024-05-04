<?php

namespace App\Controllers;

use App\Models\AuthModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;


class Auth extends Controller
{
    protected $request;

    use ResponseTrait;
    protected $type = "json";

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);
        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = \Config\Services::session();
    }

    public function index()
    {
        return view("auth");
    }

    public function login()
    {
        $session = session();
        $model = new AuthModel();

        $username = $this->request->getGetPost("username");
        $password = $this->request->getGetPost("password");

        if (empty($username) || empty($password)) {
            $session->setFlashdata("message", "user atau password tidak boleh kosong!");
            return redirect("auth");
        }

        $user = $model->where("username", $username)->first();

        if (empty($user) || $password != $user->encrypted_password) {
            $session->setFlashdata("message", "user atau password salah!!!");
            return redirect("auth");
        }

        $session->set("logged_in", [
            "username" => $user->username,
            "nama" => $user->nama_lengkap,
            "kdStatus" => $user->kd_status
        ]);
        return redirect("/");
    }

    public function logout()
    {
        session()->destroy();
        return redirect("/");
    }
}
