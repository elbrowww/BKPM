<?php

class HomeController extends BaseController
{
    public function index()
    {
        // Beranda: arahkan ke dashboard (yang akan meminta login bila perlu)
        $this->redirect('/dashboard');
    }
}
