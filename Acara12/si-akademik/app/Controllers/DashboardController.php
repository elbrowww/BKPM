<?php

class DashboardController extends BaseController
{
    public function index()
    {
        $this->view('dashboard/index', ['title' => 'Dashboard']);
    }
}
