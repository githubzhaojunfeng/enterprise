<?php

namespace app\Home\controller;

use think\Controller;

class Job extends Controller
{
    public function job()
    {
        return view("job");
    }


    public function job_g()
    {
        return view("job_g");
    }
}

?>