<?php

namespace App\Http\Controllers\Frontend;

use Auth;
use Redis;
use Illuminate\Http\Request;
use App\Models\RealNameIdent;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class IdentController extends Controller
{
    protected static $extensions = ['png', 'jpg', 'jpeg', 'gif'];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ident = RealNameIdent::where('user_id', Auth::id())->first();

        return view('frontend.user.ident.index', compact('ident'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();

        return view('frontend.user.ident.create');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $ident = RealNameIdent::where('user_id', Auth::id())->first();

        return view('frontend.user.ident.edit', compact('ident'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $ident = RealNameIdent::where('user_id', Auth::id())->first();

        // $this->validate($request, RealNameIdent::rules(), RealNameIdent::messages());

        $data                              = $request->all();
        $data['type']                      = $request->type;
        $data['user_id']                   = $ident->user_id;
        $data['license_number']            = $request->license_number;
        $data['corporation']               = $request->corporation;
        $data['identity_card']             = $request->identity_card;
        $data['phone_number']              = $request->phone_number;
        $data['license_picture']           = $request->license_picture;
        $data['front_card_picture']        = $request->front_card_picture;
        $data['back_card_picture']         = $request->back_card_picture;
        $data['hold_card_picture']         = $request->hold_card_picture;
        $data['bank_open_account_picture'] = $request->bank_open_account_picture;
        $data['agency_agreement_picture']  = $request->agency_agreement_picture;

        if ($ident->update($data)) {

            return redirect(route('idents.index'))->with('succ', '更新成功!');
        }

        return back()->withInput()->with('updateError', '更新失败!');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $userId = Auth()->user()->parent_id ?: Auth()->id();

        // $this->validate($request, RealNameIdent::rules(), RealNameIdent::messages());

        $data                              = $request->all();
        $data['type']                      = $request->type;
        $data['user_id']                   = $userId;
        $data['license_number']            = $request->license_number;
        $data['corporation']               = $request->corporation;
        $data['identity_card']             = $request->identity_card;
        $data['phone_number']              = $request->phone_number;
        $data['license_picture']           = $request->license_picture;
        $data['front_card_picture']        = $request->front_card_picture;
        $data['back_card_picture']         = $request->back_card_picture;
        $data['hold_card_picture']         = $request->hold_card_picture;
        $data['bank_open_account_picture'] = $request->bank_open_account_picture;
        $data['agency_agreement_picture']  = $request->agency_agreement_picture;

        if (RealNameIdent::create($data)) {

            return redirect(route('idents.index'));
        }
        return back()->withInput()->with('identError', '注册失败！');
    }

    /**
     * 点击图片 ajax 上传
     * @param  Illuminate\Http\Request
     * @return json
     */
    public function uploadImages(Request $request)
    {
        if ($request->hasFile('file')) {

            $file = $request->file('file');

            $path = public_path("/resources/ident/".date('Ymd')."/");

            $imagePath = $this->uploadImage($file, $path);

            return response()->json(['code' => 1, 'path' => $imagePath]);
        }
    }

    /**
     * 图片上传
     * @param  Symfony\Component\HttpFoundation\File\UploadedFile $file 
     * @param  $path string
     * @return string
     */
    public function uploadImage(UploadedFile $file, $path)
    {   
        $extension = $file->getClientOriginalExtension();

        if ($extension && ! in_array(strtolower($extension), static::$extensions)) {

            return response()->json(['code' => 2, 'path' => $imagePath]);
        }

        if (! $file->isValid()) {

            return response()->json(['code' => 2, 'path' => $imagePath]);
        }

        if (!file_exists($path)) {

            mkdir($path, 0755, true);
        }
        $randNum = rand(1, 100000000) . rand(1, 100000000);

        $fileName = time().substr($randNum, 0, 6).'.'.$extension;

        $path = $file->move($path, $fileName);

        $path = strstr($path, '/resources');

        return str_replace('\\', '/', $path);
    }
}
