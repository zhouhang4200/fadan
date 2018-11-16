<?php

namespace App\Http\Middleware;

use App\Models\GameLevelingChannelUser;
use Closure;
use App\Models\User;

/**
 * 获取渠道用户信息
 * Class ChannelUser
 * @package App\Http\Middleware
 */
class ChannelUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        # 渠道ID
        $hasFromId = request()->has('from');

        # 如果请求参数中有from 则查找用户并写入session
        if ($hasFromId) {
            # 获取真实用户ID
            $userId = hashid_decode(request('from'));
            # 查找用户
            $user = User::find($userId);
            # 能获取到则将用户Id写入session 则抛404
            if ($user) {
                $request->session()->put('user_id', $user->id);
            } else {
                abort(404);
            }
        }
        # 如果请求中没有from，而session 中也没有用户ID, 则抛404
        if (!$request->session()->get('user_id')) {
            abort(404);
        }

        # 渠道终端用户ID
        $hasUUID = request()->has('uuid');

        # 如果请求参数中有uuid 则查找或创建用户并将用户Id写入session
        if ($hasUUID) {
            # 查找或创建用户
            $channelUser = GameLevelingChannelUser::firstOrCreate([
                'user_id' => $request->session()->get('user_id'),
                'uuid' => request('uuid'),
            ]);
            # 能获取到则将渠道的终端用户Id写入session 则抛404
            if ($channelUser) {
                $request->session()->put('channel_user_id', $channelUser->id);
            }
        }
        # 如果请求中没有uuid，而session 中也没有用户uuid, 则抛404
        if (!$request->session()->get('channel_user_id')) {
            abort(404);
        }
        return $next($request);
    }
}
