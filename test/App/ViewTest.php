<?php

namespace FebriAnandaLubis\Belajar\PHP\MVC\App;

use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase
{
    // melakukan test render ke halaman index.php
    public function testRender()
    {
        View::render('Home/index', [
            "PHP Login Management"
        ]);

        // expectOutputRegex berfungsi mengecek kode html lalu dicetak di console
        $this->expectOutputRegex('[PHP Login Management]');
        $this->expectOutputRegex('[html]');
        $this->expectOutputRegex('[body]');
        $this->expectOutputRegex('[Login Management]');
        $this->expectOutputRegex('[Login]');
        $this->expectOutputRegex('[Register]');
    }

}
