<?php

namespace App\Http\Controllers\Api\App;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class TestController extends Controller
{
    public function index(Request $request)
    {
        if (!isset($request->params['name']) || !isset($request->params['version'])) {
            return response()->jsonReturn(0, '参数不正确');
        }

        try {
            // $result = VersionRepository::market($request->params['name'], $request->params['version']);
        }
        catch (CustomException $e) {
            return response()->jsonReturn(0, $e->getMessage());
        }

        return response()->jsonReturn(1, 'success', $result);
    }
}
