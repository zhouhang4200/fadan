<?php

namespace App\Http\Controllers\Backend;

use App\Models\Revision;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TemplateController extends Controller
{
    public function form()
    {
        return view('backend.template.form');
    }

    public function icon1()
    {
        return view('backend.template.icon1');
    }

    public function icon2()
    {
        return view('backend.template.icon2');
    }
}