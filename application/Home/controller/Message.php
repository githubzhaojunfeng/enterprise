<?php

namespace app\Home\controller;

use think\Controller;

class Message extends Controller
{
    public function message()
    {
        return view("message");
    }
}

?>