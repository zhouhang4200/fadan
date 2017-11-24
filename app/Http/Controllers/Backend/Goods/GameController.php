<?php
namespace App\Http\Controllers\Backend\Goods;

use Auth, \Exception;
use App\Models\Game;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GameController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $name = $request->name;

        $allGames = Game::get();

        $filters = compact('name');

        $games = Game::with(['createdAdmin', 'updatedAdmin'])
            ->filter($filters)
            ->orderBy('sortord')
            ->paginate(30);

        return view('backend.goods.game.index', compact('games', 'name', 'allGames'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        return Game::find($id);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request)
    {
        try {
            $data = $request->data;
            $data['updated_admin_user_id'] = Auth::user()->id;
            Game::where('id', $request->id)->update($data);
            return response()->json(['code' => 1, 'message' => '修改成功']);
        } catch (Exception $exception) {
            return response()->json(['code' => 0, 'message' => '修改失败']);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $data = $request->data;
            $data['created_admin_user_id'] = Auth::user()->id;
            $data['updated_admin_user_id'] = Auth::user()->id;
            Game::create($data);
            return response()->json(['code' => 1, 'message' => '添加成功']);
        } catch (Exception $exception) {
            return response()->json(['code' => 0, 'message' => '添加失败']);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function status(Request $request)
    {
        $game = Game::find($request->id);
        if ($game) {
            $game->status = $request->status;
            $game->created_admin_user_id = Auth::user()->id;
            $game->save();
            return response()->json(['code' => 1, 'message' => '修改成功']);
        } else {
            return response()->json(['code' => 0, 'message' => '修改失败']);
        }
    }
}
