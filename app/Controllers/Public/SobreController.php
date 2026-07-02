<?php

namespace App\Controllers\Public;
use App\Core\Controller;

class SobreController extends Controller
{
    public function index()
    {
        $this->view('public/sobre', [
            'message' => 'Olá Mundo com Twig'
        ]);
    }
}