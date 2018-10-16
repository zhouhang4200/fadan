<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\RedisConnect;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * 设置以username登录
     * @return [type] [description]
     */
    public function username()
    {
        return 'name';
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('frontend.v1.auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'geetest_challenge' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->ajax(0, $validator->errors()->all()[0]);
        }

        // 对前端转输数据进行解密
        $request['password'] = clientRSADecrypt($request->password);

        // 检查账号是否被禁用
        $user = User::where('name', $request->name)->first();

        if ($user && \Hash::check($request['password'], $user->password)) {
            if ($user->status == 1) {
                 return response()->ajax(0, '您的账号已被禁用!');
            }
        }

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            if ($this->sendLoginResponse($request)) {
                return response()->ajax(1, 'success');
            }
        }
        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return response()->ajax(0, '账号或密码错误');
    }

    /**
     * 登录后更改用户是否在线字段
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        User::where('id', Auth::user()->id)->update(['online' => 1]);

        $user = Auth::user();
        $redis = RedisConnect::session();
        $sessionId = $redis->get(config('redis.user')['loginSession'] . $user->id);

        if ($sessionId) {
            $redis->del($sessionId);
            $redis->del($redis->get(config('redis.user')['loginSession'] . $user->id));
        }
        $redis->set(config('redis.user')['loginSession'] . $user->id, session()->getId());

        session()->flash('notice', 'yes');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->forget($this->guard()->getName());

        $request->session()->regenerate();

        return redirect('/login');
    }

    /**
     * $this->authenticated($request, $this->guard()->user()) ? :
     * @param Request $request
     * @return bool
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        return $this->authenticated($request, $this->guard()->user()) ? : true;
    }
}
