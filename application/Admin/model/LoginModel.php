<?php

namespace app\Admin\Model;

use think\Model;
use think\Session;

class LoginModel extends Model
{
    public function checkLogined($data)
    {
        $ret = array();
        $username = $data['username'];
        $pass = $data['password'];
        //数据库操作 Db:: 或者 db('table')("助手函数")
        //如果此账号在数据库查询后，个数大于0 说明用户存在，否则就是不存在
        if (db("admin")->where("username", $username)->count() > 0) {
            $userinfo = db("admin")->where("username", $username)->find();
            if (md5($pass) == $userinfo['password']) {
                $ret['ok'] = true;
                $ret['msg'] = "登录成功";
                $session['uid'] = $userinfo['id'];
                $session['username'] = $userinfo['username'];
                $session['logo'] = $userinfo['logo'];

                $data = db("admin")->alias("a")
                    ->field("a.*,ra.role_id,ac.node_id")
                    ->join("role_admin ra", "a.id=ra.user_id")
                    ->join("access ac", "ra.role_id=ac.role_id")
                    ->where("a.username", $username)->find();
                $node_id = explode(",", $data['node_id']);
                //In 条件查询的时候 如果是,号隔开的字符串可以进行查询，如果是通过,号拆分为数组也可以进行查询
                $node_list = db("node")->where("id", "IN", $node_id)->select();
                //dump($node_list);
                session("ADMIN_NODES", $node_list);
                session("ADMIN_SESS", $session); //保存session
                return $ret;
            } else {
                $ret['ok'] = false;
                $ret['msg'] = "密码错误";
                return $ret;
            }
        } else {
            $ret['ok'] = false;
            $ret['msg'] = "此账号不存在";
            return $ret;
        }
    }


}

?>