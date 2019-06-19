<?php

namespace app\Home\controller;

use think\Controller;

class Index extends Controller
{
    public function index()
    {
        $res = db("nav")->select();

        return view("index");
    }
}

?>