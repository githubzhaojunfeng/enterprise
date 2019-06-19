<?php

namespace app\Admin\Model;

use think\Model;

class AdminModel extends Model
{
    protected $table = "tp_admin";

    public function getStatusAttr($value)
    {
        $status = [0 => "<font color='red'>×</font>", 1 => "<font color='green'>√</font>"];
        return $status[$value];
    }

    public function getSexAttr($value)
    {
        //声明的数组 键都是数字类型
        $sex = [0 => "女", 1 => "男"];
        //传过来的值当做数组的索引(键)
        return $sex[$value];
    }


    /*通过管理员账号查询个数*/
    public function getCountAdminByName($username)
    {
        $count = $this->where("username", $username)->count();
        return $count;
    }
}

?>