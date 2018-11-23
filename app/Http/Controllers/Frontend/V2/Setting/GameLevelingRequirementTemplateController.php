<?php

namespace App\Http\Controllers\Frontend\V2\Setting;

use App\Http\Controllers\Controller;
use App\Models\GameLevelingRequirementsTemplate;

/**
 * 代练要求模板
 * Class GameLevelingRequirementTemplateController
 * @package App\Http\Controllers\Frontend\V2\Setting
 */
class GameLevelingRequirementTemplateController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $template = GameLevelingRequirementsTemplate::where(['user_id' => request()->user()->getPrimaryUserId()])
            ->orderBy('game_id')
            ->orderBy('status')
            ->get();

        return response()->json(['status', 'data' => $template]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function show()
    {
        return response()->json(['status', 'data' => GameLevelingRequirementsTemplate::find(request('id'))]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function store()
    {
        if (request('id') == 0) {
            if (request('status')) {
                GameLevelingRequirementsTemplate::where('user_id', auth()->user()->getPrimaryUserId())
                    ->where('game_id', request('game_id'))
                    ->update(['status' => 0]);
            }
            GameLevelingRequirementsTemplate::create([
                'user_id' => auth()->user()->getPrimaryUserId(),
                'game_id' => request('game_id'),
                'status' => request('status'),
                'name' => request('name'),
                'content' => request('content'),
            ]);

            return response()->json(['status' => 1, 'message' => '添加成功']);
        } else {
            $template = GameLevelingRequirementsTemplate::where('id', request('id'))
                ->where('user_id', auth()->user()->getPrimaryUserId())
                ->first();
            if ($template) {
                $template->game_id = request('game_id');
                $template->status = request('status');
                $template->name = request('name');
                $template->content = request('content');
                $template->save();
            }
            if (request('status')) {
                GameLevelingRequirementsTemplate::where('user_id', auth()->user()->getPrimaryUserId())
                    ->where('id', '!=', $template->id)
                    ->where('game_id',  $template->game_id)
                    ->update(['status' => 0]);
            }
            return response()->json(['status' => 1, 'message' => '修改成功']);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function update()
    {
        try {
            GameLevelingRequirementsTemplate::where([
                'id' => request('id'),
                'user_id' => request()->user()->getPrimaryUserId(),
            ])->update([
                'name' => request('name'),
                'content' => request('content'),
                'game_id' => request('game_id'),
            ]);
        } catch (\Exception $exception) {
            return response()->json(['status' => 0, 'message' => 'fail']);
        }
        return response()->json(['status' => 1, 'message' => 'success']);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete()
    {
        try {
            GameLevelingRequirementsTemplate::where([
                'id' => request('id'),
                'user_id' => request()->user()->getPrimaryUserId(),
            ])->delete();
        } catch (\Exception $exception) {
            return response()->json(['status' => 0, 'message' => 'fail']);
        }
        return response()->json(['status' => 1, 'message' => 'success']);
    }
}