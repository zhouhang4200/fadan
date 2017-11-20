<?php
namespace App\Http\Controllers\Backend\User;

use App\Models\User;
use Auth, \Exception;
use App\Models\Game;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WeightController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $userId = $request->user_id;

        $userWeight = User::with(['createdAdmin', 'updatedAdmin'])->paginate(30);

        return view('backend.user.weight.index')->with([
            'userWeight' => $userWeight
        ]);
    }
}