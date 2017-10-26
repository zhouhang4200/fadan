<?php
namespace App\Services\Views;

class WidthdrawService
{
    public function button($bottomText = '提现', $domClass = 'layui-btn layui-btn-normal', $domStyle = '')
    {
        echo view('frontend.public.withdraw.pop-up-window', compact('bottomText', 'domClass', 'domStyle'))->render();
    }
}
