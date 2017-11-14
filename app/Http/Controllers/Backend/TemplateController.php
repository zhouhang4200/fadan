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

    public function icons1()
    {
        return view('backend.template.icons1');
    }

    public function icons2()
    {
        return view('backend.template.icons2');
    }
}