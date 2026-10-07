<?php

class HomeController extends Controller
{
    public function index()
    {
        // Beranda: arahkan ke dashboard (yang akan meminta login bila perlu)
        $this->redirect('/dashboard');
    }
}
