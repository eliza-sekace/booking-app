<?php
namespace App\Controllers;

use App\Views\View;


class HomepageController
{
public function index()
{
return new View("homepage.html", [ ]);
}
}