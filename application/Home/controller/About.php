<?php

namespace app\Home\controller;

use think\Controller;

class About extends Controller
{
    public function about()
    {
        return view("about");
    }
}

?>