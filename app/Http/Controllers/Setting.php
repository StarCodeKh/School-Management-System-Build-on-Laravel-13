<?php

namespace App\Http\Controllers;

class Setting extends Controller
{
    // index page setting
    public function index()
    {
        return view('setting.settings');
    }
}
