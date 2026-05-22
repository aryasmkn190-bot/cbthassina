<?php

namespace App\Controllers;

class HomeController extends BaseController
{
    public function maintenance()
    {
        return view('maintenance'); // Mengembalikan view 'maintenance'
    }
    public function notFound()
    {
        return view('404notfound'); // Mengembalikan view 'maintenance'
    }
    public function index()
    {

        $data = [
            'title' => 'Dashboard',
            'setting' => $this->appSetting(),
        ];
        return view('Panel/home', $data);
    }
}
