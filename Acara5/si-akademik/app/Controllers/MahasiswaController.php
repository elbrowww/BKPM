<?php

class MahasiswaController
{
    public function index()
    {
        echo "MahasiswaController::index()";
    }

    public function create()
    {
        echo "MahasiswaController::create()";
    }

    public function show($id)
    {
        echo "MahasiswaController::show() dengan id = " . (int) $id;
    }
}
