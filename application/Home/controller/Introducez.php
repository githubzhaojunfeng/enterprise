<?php

namespace app\Home\controller;

use think\Controller;

class Introducez extends Controller
{

    public function introduce_z()
    {
        return view("introduce_z");
    }

    public function introduce_r()
    {
        return $this->fetch();
    }

}

?>