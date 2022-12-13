<?php

namespace FebriAnandaLubis\Belajar\PHP\MVC\Repository;

use FebriAnandaLubis\Belajar\PHP\MVC\Config\Database;
use FebriAnandaLubis\Belajar\PHP\MVC\Domain\User;
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase
{
    private UserRepository $userRepository;

    // setup adalah function yang pertama kali di proses sebelum unit test
    // agar data yang di unit test selalu clear/bersih
    protected function setUp(): void
    {
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->userRepository->deleteAll();
    }

    // melakukan test jika register user sukses
    public function testSuccess()
    {
        $user = new User();
        $user->id = "febri";
        $user->name = "Febri";
        $user->password = "rahasia";

        // memasukan data dari Domain User
        $this->userRepository->save($user);

        // melakukan query database dengan berdasarkan Id User yang di return
        $result = $this->userRepository->findById($user->id);

        // menyatakan 2 variabel atau nilai sama
        // parameter pertama adalah data dari user
        // yang kedua adalah data dari database
        self::assertEquals($user->id, $result->id);
        self::assertEquals($user->name, $result->name);
        self::assertEquals($user->password, $result->password);
    }

    // Melakukan fungsi dimana jika query berdasarkan id yang tidak ada
    public function testFindByIdNotFound()
    {
        $user = $this->userRepository->findById('notfound');

        // menyatkan bahwa variabel memiliki nilai null/kosong/tidak ada
        self::assertNull($user);
    }


}
