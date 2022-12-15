<?php

namespace FebriAnandaLubis\Belajar\PHP\MVC\App {
    function header(string $value) {
        echo $value;
    }
}

namespace FebriAnandaLubis\Belajar\PHP\MVC\Controller {
    use FebriAnandaLubis\Belajar\PHP\MVC\Config\Database;
    use FebriAnandaLubis\Belajar\PHP\MVC\Domain\User;
    use FebriAnandaLubis\Belajar\PHP\MVC\Repository\UserRepository;
    use PHPUnit\Framework\TestCase;

    class UserControllerTest extends TestCase
    {
        private UserController $userController;
        private UserRepository $userRepository;

        // metode setup adalah menjalankan sebuah fungsi sebelum unit testnya dipanggil
        protected function setUp(): void
        {
            $this->userController = new UserController();

            // hapus datanya semua
            // userRepository menerima 1 paramater connection database
            $this->userRepository = new UserRepository(Database::getConnection());
            $this->userRepository->deleteAll();

            // mengisi/menetapkan variabel lingkungan yang berada di View.php
            putenv("mode=test");
        }

        // ini berfungsi untuk merender halaman html, saat melakukan render halaman register
        public function testRegister()
        {
            $this->userController->register();

            // berepektasi bahwa ada di halaman html memiliki kata di bawah ini
            $this->expectOutputRegex('[Register]');
            $this->expectOutputRegex('[Id]');
            $this->expectOutputRegex('[Name]');
            $this->expectOutputRegex('[Password]');
            $this->expectOutputRegex('[Register new User]');
        }

        // fungsi untuk mengetahui jika registrasi sukses dan respone halamannya
        public function testPostRegisterSuccess()
        {
            $_POST['id'] = 'febri';
            $_POST['name'] = 'Febri';
            $_POST['password'] = 'rahasia';

            // harus diperhatikan ketika test register halaman di redirect perlu menampilkan ekspektasi apa
            $this->userController->postRegister();

            // berepektasi dengan sebuah output regex di bawah yang di ambil dari value headernya
            $this->expectOutputRegex("[Location: /users/login]");
        }

        // fungsi untuk menjalankan validation error jika gagal registrasi
        public function testPostRegisterValidationError()
        {
            // test error maka data harus dikosongkan
            $_POST['id'] = '';
            $_POST['name'] = 'Febri';
            $_POST['password'] = 'rahasia';

            $this->userController->postRegister();

            // berepektasi bahwa ada error
            $this->expectOutputRegex('[Register]');
            $this->expectOutputRegex('[Id]');
            $this->expectOutputRegex('[Name]');
            $this->expectOutputRegex('[Password]');
            $this->expectOutputRegex('[Register new User]');
            $this->expectOutputRegex('[Id, Name, Password can not blank]');
        }

        // fungsi untuk menjalankan jika terjadi error
        public function testPostRegisterDuplicate()
        {
            // masukan data baru ke database untuk test, harus sama seperti di data yang dibawah
            $user = new User();
            $user->id = 'febri';
            $user->name = 'Febri';
            $user->password = 'rahasia';

            // lalu datanya di simpan
            $this->userRepository->save($user);

            $_POST['id'] = 'febri';
            $_POST['name'] = 'Febri';
            $_POST['password'] = 'rahasia';

            $this->userController->postRegister();

            // berepektasi bahwa ada data yang sama/ user yang sama
            $this->expectOutputRegex('[Register]');
            $this->expectOutputRegex('[Id]');
            $this->expectOutputRegex('[Name]');
            $this->expectOutputRegex('[Password]');
            $this->expectOutputRegex('[Register new User]');
            $this->expectOutputRegex('[User Id already axists]');
        }

        // test untuk merender halaman form loginnya
        public function testLogin()
        {
            $this->userController->login();

            // melakukan expektasi jika ada sebuah regex/kata di dalam halaman yang dirender di view
            $this->expectOutputRegex('[Login user]');
            $this->expectOutputRegex('[Id]');
            $this->expectOutputRegex('[Password]');
        }

        // test untuk jika user berhasil login dan halamannya di redirect
        public function testLoginSuccess()
        {
            // masukan user/ register user terlebih dahulu
            $user = new User();
            $user->id = 'febri';
            $user->name = 'Febri';

            // password di bikin bcrypt/diamankan
            $user->password = password_hash('rahasia', PASSWORD_BCRYPT);
            // lalu datanya di simpan
            $this->userRepository->save($user);

            // lalu kita login dengan sesuai data di atas
            // nah maksud data post adalah data yang dikirimkan pengganti form, dan datanya diterima oleh function postLogin()
            $_POST['id'] = 'febri';
            $_POST['password'] = 'rahasia';

            $this->userController->postLogin();

            // melakukan expektasi jika ada sebuah regex/kata di dalam halaman yang dirender di view
            $this->expectOutputRegex('[Location: /]');
        }

        // test untuk memunculkan validasi/peringatan jika error/gagal
        public function testLoginValidationError()
        {
            // kita bikin validasi jika datanya kosong
            $_POST['id'] = '';
            $_POST['password'] = '';

            // nah maksud data post adalah data yang dikirimkan pengganti form, dan datanya diterima oleh function postLogin()
            $this->userController->postLogin();

            // melakukan expektasi jika ada sebuah regex/kata di dalam halaman yang dirender di view
            $this->expectOutputRegex('[Login user]');
            $this->expectOutputRegex('[Id]');
            $this->expectOutputRegex('[Password]');
            // harus ada expectOutput error jika gagal
            $this->expectOutputRegex('[Id, Password can not blank]');
        }

        // test untuk jika user login tidak ada
        public function testLoginUserNotFound()
        {
            // kita bikin validasi jika user tidak ada
            $_POST['id'] = 'notfound';
            $_POST['password'] = 'notfound';

            // nah maksud data post adalah data yang dikirimkan pengganti form, dan datanya diterima oleh function postLogin()
            $this->userController->postLogin();

            // melakukan expektasi jika ada sebuah regex/kata di dalam halaman yang dirender di view
            $this->expectOutputRegex('[Login user]');
            $this->expectOutputRegex('[Id]');
            $this->expectOutputRegex('[Password]');
            // harus ada expectOutput error jika user tidak ada
            $this->expectOutputRegex('[Id or password is wrong]');
        }

        // test untuk jika user salah id/password
        public function testWrongPassword()
        {
            // masukan user/ register user terlebih dahulu
            $user = new User();
            $user->id = 'febri';
            $user->name = 'Febri';

            // password di bikin bcrypt/diamankan
            $user->password = password_hash('rahasia', PASSWORD_BCRYPT);
            // lalu datanya di simpan
            $this->userRepository->save($user);

            // kita bikin validasi jika id/password salah
            // disini contoh data loginnya id benar, dan passwordnya salah
            $_POST['id'] = 'febri';
            $_POST['password'] = 'salah';

            // nah maksud data post adalah data yang dikirimkan pengganti form, dan datanya diterima oleh function postLogin()
            $this->userController->postLogin();

            // melakukan expektasi jika ada sebuah regex/kata di dalam halaman yang dirender di view
            $this->expectOutputRegex('[Login user]');
            $this->expectOutputRegex('[Id]');
            $this->expectOutputRegex('[Password]');
            // harus ada expectOutput error jika user tidak ada
            $this->expectOutputRegex('[Id or password is wrong]');
        }

    }
}


