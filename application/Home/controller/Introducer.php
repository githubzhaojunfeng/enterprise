<?php

namespace app\Home\controller;

use think\Controller;

class Introducer extends Controller
{

    public function introduce_r()
    {
        return $this->fetch();
    }

}

?>