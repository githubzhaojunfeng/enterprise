<?php

namespace app\Home\controller;

use think\Controller;

class Feedback extends Controller
{
    public function feedback()
    {
        return view("feedback");
    }

}

?>