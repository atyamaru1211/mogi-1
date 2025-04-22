<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class SellController extends Controller
{
    public function create()
    {
        return view('sell.create');
    }

    public function store()
    {}
}
