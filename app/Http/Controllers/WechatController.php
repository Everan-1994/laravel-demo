<?php

namespace App\Http\Controllers;

class WechatController extends Controller
{
    protected $app;

    public function __construct()
    {
        $this->app = app('wechat.official_account');
    }

    public function index()
    {
        $response = $this->app->oauth->scopes(['snsapi_userinfo'])->redirect(env('APP_URL') . '/api/user');

        return $response;
    }

    public function user()
    {
        $user = $this->app->oauth->user();

        dd($user);
    }

    public function code()
    {

        $result = $this->app->qrcode->temporary('foo', 600);
        $qrcodeUrl = $this->app->qrcode->url($result['ticket']);

        return view('welcome', compact('qrcodeUrl'));
    }

    public function serve()
    {
        $this->app->server->push(function ($message) {
            if ($message['Event'] === 'SCAN') {
                $openid = $message['FromUserName'];
                return '';
            } else {

                return '登录失败';
            }
        }, \EasyWeChat\Kernel\Messages\Message::EVENT);

        return $this->app->server->serve();
    }
}
