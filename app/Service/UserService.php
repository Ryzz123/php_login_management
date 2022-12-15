<?php

namespace FebriAnandaLubis\Belajar\PHP\MVC\Service;

use FebriAnandaLubis\Belajar\PHP\MVC\Config\Database;
use FebriAnandaLubis\Belajar\PHP\MVC\Domain\User;
use FebriAnandaLubis\Belajar\PHP\MVC\Exception\ValidationException;
use FebriAnandaLubis\Belajar\PHP\MVC\Model\UserLoginRequest;
use FebriAnandaLubis\Belajar\PHP\MVC\Model\UserLoginResponse;
use FebriAnandaLubis\Belajar\PHP\MVC\Model\UserRegisterRequest;
use FebriAnandaLubis\Belajar\PHP\MVC\Model\UserRegisterResponse;
use FebriAnandaLubis\Belajar\PHP\MVC\Repository\UserRepository;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    // melakukan logic register
    public function register(UserRegisterRequest $request): UserRegisterResponse
    {
        $this->validateUserRegistrationRequest($request);

        // menggunakan try catch jika ada kesalahan maka di rollback
        try {
            // mulai database begin transaction
            Database::beginTransaction();

            // kita akan cek dulu
            // jika id user tersebut sudah ada
            $user = $this->userRepository->findById($request->id);

            // jika user ada atau tidak sama dengan null/kosong
            if ($user != null) {
                throw new ValidationException("User Id already axists"); // artinya user sudah ada
            }

            $user = new User();
            $user->id = $request->id;
            $user->name = $request->name;

            // melakukan hashing password
            // agar password lebih aman
            $user->password = password_hash($request->password, PASSWORD_BCRYPT);

            // simpan data usernya
            $this->userRepository->save($user);

            // membuat response
            $response = new UserRegisterResponse();
            $response->user = $user;

            // sebelum return semua proses berjalan lancar lakukan database commit
            Database::commitTransaction();
            return $response;
        } catch (\Exception $exception) {
            // jika ada kesalahan atau error maka bisa di rollback dan di kembalikan
            Database::rollbackTransaction();
            // munculkan kesalahannya
            throw $exception;
        }
    }

    // digunakan untuk validasi regsitrasi/jika datanya kosong
    private function validateUserRegistrationRequest(UserRegisterRequest $request)
    {
        if ($request->id == null || $request->name == null || $request->password == null || trim($request->id) == "" || trim($request->name) == "" || trim($request->password) == "") {
            // kalau dia error
            throw new ValidationException("Id, Name, Password can not blank");
        }
    }

    // function untuk melakukan login
    public function login(UserLoginRequest $request): UserLoginResponse
    {
        $this->validateUserLoginRequest($request);

        $user = $this->userRepository->findById($request->id);

        // jika user tidak ada/null
        if ($user == null) {
            throw new ValidationException("Id or password is wrong");
        }

        // untuk mengecek password user request sama dengan password yang berada di database
        if (password_verify($request->password, $user->password)) {
            $response = new UserLoginResponse();
            $response->user = $user;
            return $response;
        } else {
            throw new ValidationException("Id or password is wrong");
        }
    }

    // digunakan untuk validasi login/jika datanya kosong
    private function validateUserLoginRequest(UserLoginRequest $request)
    {
        if ($request->id == null || $request->password == null || trim($request->id) == "" || trim($request->password) == "") {
            // kalau dia error
            throw new ValidationException("Id, Password can not blank");
        }
    }
}