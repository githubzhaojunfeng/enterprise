<?php

namespace app\Home\controller;

use think\Controller;

class Article extends Controller
{
    public function article()
    {
        return view("article");
    }
}

?>