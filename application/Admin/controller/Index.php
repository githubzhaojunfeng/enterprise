<?php

namespace app\Admin\Controller;

use app\Admin\model\AdminModel;

class Index extends Common
{
    public function index()
    {
        return $this->fetch();
    }

}

?>