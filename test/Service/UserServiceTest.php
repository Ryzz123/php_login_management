<?php

namespace FebriAnandaLubis\Belajar\PHP\MVC\Service;

use FebriAnandaLubis\Belajar\PHP\MVC\Config\Database;
use FebriAnandaLubis\Belajar\PHP\MVC\Domain\User;
use FebriAnandaLubis\Belajar\PHP\MVC\Exception\ValidationException;
use FebriAnandaLubis\Belajar\PHP\MVC\Model\UserLoginRequest;
use FebriAnandaLubis\Belajar\PHP\MVC\Model\UserRegisterRequest;
use FebriAnandaLubis\Belajar\PHP\MVC\Repository\UserRepository;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{
    private UserService $userService;
    private UserRepository $userRepository;

    // setup dipanggil sebelum unit test dibawahnya dijalankan
    protected function setUp(): void
    {
        $connection = Database::getConnection();

        // user repository butuh satu paramater connection/pdo
        $this->userRepository = new UserRepository($connection);
        // user service membutuhkan satu paramater user repository/query database
        $this->userService = new UserService($this->userRepository);

        // lakukan delete all
        $this->userRepository->deleteAll();
    }

    // melakukan test jika registrasi berhasil
    public function testRegisterSucces()
    {
        $request = new UserRegisterRequest();
        $request->id = "febri";
        $request->name = 'Febri';
        $request->password = 'rahasia';

        $response = $this->userService->register($request);

        // berfungsi menyatakan dua nilai/variabel sama
        self::assertEquals($request->id, $response->user->id);
        self::assertEquals($request->name, $response->user->name);

        // Menegaskan bahwa dua variabel tidak sama.
        // bahwa password yang dari request/inputan tidak sama dengan yg sudah di hash
        self::assertNotEquals($request->password, $response->user->password);

        // Menegaskan bahwa suatu kondisi benar.
        // password verify adalah untuk memverifikasi bahwa password request/yg masuk itu sama atau tidak
        self::assertTrue(password_verify($request->password, $response->user->password));
    }

    // melakukan test jika registrasi gagal
    public function testRegisterFailed()
    {
        // test ini bertujuan untuk test jika gagal dan mengetest keluran throw exceptionnya/kesalahannya
        $this->expectException(ValidationException::class);

        $request = new UserRegisterRequest();
        $request->id = "";
        $request->name = '';
        $request->password = '';

        $this->userService->register($request);
    }

    // melakukan test jika ada data yang duplikat atau sama
    public function testRegisterDuplicate()
    {
        $user = new User();
        $user->id = "febri";
        $user->name = "Febri";
        $user->password = "rahasia";

        // melakukan save ke database datanya
        $this->userRepository->save($user);

        // berfungsi untuk menyatakan bahwa ada kesalahan yang muncul
        // yaitu kesalahan data yang sama
        $this->expectException(ValidationException::class);

        // melakukan registrasi lagi, menggunakan data yang sama seperti data yang sudah dimasukkan diatas
        $request = new UserRegisterRequest();
        $request->id = "febri";
        $request->name = 'Febri';
        $request->password = 'rahasia';

        $this->userService->register($request);
    }

    // unit test untuk mengetes jika user login tidak ada
    public function testLoginNotFound()
    {
        // expectasi jika gagal login
        $this->expectException(ValidationException::class);

        $request = new UserLoginRequest();
        $request->id = 'febri';
        $request->password = 'febri';

        $this->userService->login($request);
    }

    // unit test untuk test login jika password salah
    public function testLoginWrongPassword()
    {
        // untuk test password salah adalah kita harus bikin user terlebih dahulu
        $user = new User();
        $user->id = 'febri';
        $user->name = 'Febri';

        // bikin encrypt/pengamanan password
        $user->password = password_hash("febri", PASSWORD_BCRYPT);

        // expectasi jika gagal login
        $this->expectException(ValidationException::class);

        $request = new UserLoginRequest();
        $request->id = 'febri';
        $request->password = 'salah';

        $this->userService->login($request);
    }

    // unit test untuk test login jika berhasil
    public function testLoginSuccess()
    {
        // untuk test login berhasil
        $user = new User();
        $user->id = 'febri';
        $user->name = 'Febri';

        // bikin encrypt/pengamanan password
        $user->password = password_hash("febri", PASSWORD_BCRYPT);

        // expectasi berhasil login
        $this->expectException(ValidationException::class);

        $request = new UserLoginRequest();
        $request->id = 'febri';
        $request->password = 'febri';

        $response = $this->userService->login($request);

        // test untuk jika dua nilai nya sama dari id dan passwordnya
        self::assertEquals($request->id, $response->user->id);

        // assert true untuk test jika nilainya sama maka true/ benar
        self::assertTrue(password_verify($request->password, $response->user->password));
    }

}
