<?php

namespace FebriAnandaLubis\Belajar\PHP\MVC\Controller;

use FebriAnandaLubis\Belajar\PHP\MVC\App\View;
use FebriAnandaLubis\Belajar\PHP\MVC\Config\Database;
use FebriAnandaLubis\Belajar\PHP\MVC\Exception\ValidationException;
use FebriAnandaLubis\Belajar\PHP\MVC\Model\UserLoginRequest;
use FebriAnandaLubis\Belajar\PHP\MVC\Model\UserRegisterRequest;
use FebriAnandaLubis\Belajar\PHP\MVC\Repository\UserRepository;
use FebriAnandaLubis\Belajar\PHP\MVC\Service\UserService;

class UserController
{
    // harus membuat 2 function
    // 1 menampilkan form nya
    // 2 aksi untuk form nya

    private UserService $userService;

    // menggunakan constructor dengan paramater kosong
    public function __construct()
    {
        $connection = Database::getConnection();
        // user repository butuh satu paramater connection/pdo
        $userRepository = new UserRepository($connection);
        // user service membutuhkan satu paramater user repository/query database
        $this->userService = new UserService($userRepository);
    }

    // function untuk render halaman registrasi
    public function register()
    {
        View::render('User/register', [
            'title' => 'Register new User'
        ]);
    }

    // function untuk ambil aksi method GET/POST
    public function postRegister()
    {
        // panggil userRegisterRequest dari model
        $request = new UserRegisterRequest();
        // untuk dikirimkan di servicenya
        $request->id = $_POST['id'];
        $request->name = $_POST['name'];
        $request->password = $_POST['password'];

        // lalu data dikirimkan ke userService register
        // lakukan try catch jika terjadi error
        try {
            // variable $request otomatis isinya semua data yang masuk di model
            $this->userService->register($request);
            // jika sukse maka redirect halamannya ke /users/login
            View::redirect('/users/login');
        } catch (ValidationException $exception) {
            // jika terjadi error munculkan lagi halaman home nya
            View::render('User/register', [
                'title' => 'Register new User',
                'error' => $exception->getMessage()
            ]);
        }
    }

    // function untuk render halaman login
    public function login() {
        View::render('User/login', [
            "title" => "Login user"
        ]);
    }

    // function untuk cek login POST dari user
    public function postLogin() {
        // panggil userLoginRequest dari model
        $request = new UserLoginRequest();
        $request->id = $_POST['id'];
        $request->password = $_POST['password'];

       // lakukan try catch jika terjadi error
        try {
            $this->userService->login($request);
            // setelah login berhasil user di redirect ke halaman home
            View::redirect('/');
        } catch (ValidationException $exception) {
            View::render('User/login', [
                "title" => "Login user",
                "error" => $exception->getMessage()
            ]);
        }
    }
}