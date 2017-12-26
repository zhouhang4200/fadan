<?php

namespace App\Http\Controllers\Api\App;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exceptions\CustomException;
use App\Repositories\Api\VersionRepository;


class VersionController extends Controller
{
    public function ios(Request $request)
    {
        if (!isset($request->params['version'])) {
            return response()->jsonReturn(0, '参数不正确');
        }

        try {
            $result = VersionRepository::ios($request->params['version']);
        }
        catch (CustomException $e) {
            return response()->jsonReturn(0, $e->getMessage());
        }

        return response()->jsonReturn(1, 'success', $result);
    }
}
