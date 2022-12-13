<?php

namespace FebriAnandaLubis\Belajar\PHP\MVC\App;

class View
{
    // render untuk tampilkan halaman
    public static function render(string $view, $model)
    {
        require __DIR__ . '/../View/header.php';
        require __DIR__ . '/../View/' . $view . '.php';
        require __DIR__ . '/../View/footer.php';
    }

    // redirect untuk memindahkan halaman
    public static function redirect(string $url) {
        header("Location: $url");

        // cek untuk exit di unit test
        // getenv berfungsi untuk mendapatkan nilai dari variabel lingkungan
        if (getenv("mode") != "test") {
            exit();
        }
    }
}