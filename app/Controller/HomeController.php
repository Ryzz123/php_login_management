<?php
namespace FebriAnandaLubis\Belajar\PHP\MVC\Controller;

use FebriAnandaLubis\Belajar\PHP\MVC\App\View;

class HomeController
{
    // function controller untuk memanggil halaman index.php yang berada di folder View/Home
    function index() {
        View::render('Home/index', [
            "title" => "PHP Login Management"
        ]);

    }
}