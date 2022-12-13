<?php

namespace FebriAnandaLubis\Belajar\PHP\MVC\Repository;

use FebriAnandaLubis\Belajar\PHP\MVC\Domain\User;

class UserRepository
{
    private \PDO $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    // function untuk melakukan registrasi
    public function save(User $user): User
    {
        $statement = $this->connection->prepare("INSERT INTO users(id, name, password) VALUES (?,?,?)");
        $statement->execute([
            $user->id,
            $user->name,
            $user->password
        ]);
        return $user;
    }

    // function tambahan mengambil data berdasarkan id
    public function findById(string $id): ?User
    {
        $statement = $this->connection->prepare("SELECT id, name, password FROM users WHERE id = ?");
        $statement->execute([$id]);

        // untuk melakukan close cursor
        try {
            if ($row = $statement->fetch()) {
                $user = new User();
                $user->id = $row['id'];
                $user->name = $row['name'];
                $user->password = $row['password'];
                return $user;
            } else {
                return null;
            }
        } finally {
            $statement->closeCursor();
        }
    }

    // function untuk delete semua datanya
    public function deleteAll(): void
    {
        $this->connection->exec("DELETE FROM users");
    }
}