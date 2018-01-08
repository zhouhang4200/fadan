<?php

namespace App\Http\Controllers\Backend\App;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exceptions\CustomException;
use App\Repositories\Backend\VersionRepository;

class VersionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $name = $request->name;
        $dataList = VersionRepository::dataList($name);

        $appName = config('ios.app_name');

        return view('backend.app.version.index', compact('dataList', 'name', 'appName'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $name         = trim($request->name);
        $number       = trim($request->number);
        $remark       = trim($request->remark);
        $forcedUpdate = trim($request->forced_update);

        try {
            VersionRepository::create($name, $number, $remark, $forcedUpdate);
        }
        catch (CustomException $e) {
            return response()->ajax(0, $e->getMessage());
        }

        return response()->ajax(1);
    }
}
