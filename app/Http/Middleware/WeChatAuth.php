<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Event;
use http\Env\Request;
use Overtrue\LaravelWeChat\Events\WeChatUserAuthorized;

class WeChatAuth
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param string|null              $scopes
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $account = 'default', $scopes = null)
    {
        // $account 与 $scopes 写反的情况
        if (is_array($scopes) || (\is_string($account) && str_is('snsapi_*', $account))) {
            list($account, $scopes) = [$scopes, $account];
            $account || $account = 'default';
        }

        $isNewSession = false;
        $sessionKey = \sprintf('wechat.oauth_user.%s', $account);
        $config = config(\sprintf('wechat.official_account.%s', $account), []);
        $officialAccount = app(\sprintf('wechat.official_account.%s', $account));
        $scopes = $scopes ?: array_get($config, 'oauth.scopes', ['snsapi_base']);

        if (is_string($scopes)) {
            $scopes = array_map('trim', explode(',', $scopes));
        }

        $session = session($sessionKey, []);

        myLog('session', ['uri' => $request->fullUrl(), 'id' => session()->getId(), 'se' => $session]);
        if (!$session) {
            if ($request->has('code')) {
                $user = $officialAccount->oauth->user();
                session([$sessionKey => $user ?? []]);
                $isNewSession = true;

                myLog('session', ['uri' => $request->fullUrl(), 'id' => session()->getId(), $user]);

                Event::fire(new WeChatUserAuthorized(session($sessionKey), $isNewSession, $account));

                return redirect()->to($this->getTargetUrl($request));
            }

            session()->forget($sessionKey);

            return $officialAccount->oauth->scopes($scopes)->redirect($request->fullUrl());
        }

        Event::fire(new WeChatUserAuthorized(session($sessionKey), $isNewSession, $account));

        return $next($request);
    }

    /**
     * Build the target business url.
     *
     * @param Request $request
     *
     * @return string
     */
    protected function getTargetUrl($request)
    {
        $queries = array_except($request->query(), ['code', 'state']);

        return $request->url().(empty($queries) ? '' : '?'.http_build_query($queries));
    }
}
