<?php

namespace app\Admin\Controller;

use think\Controller;
use think\captcha\Captcha;

class Login extends Controller
{


    public function login()
    {
        return $this->fetch();
    }

    /*
      checklogin 是控制器接收参数的方法
    */
    public function checkLogin()
    {
        $data = input("post.");
        $model = model("LoginModel");
        if (empty($data['username'])) {
            $this->error("用户名不能为空");
        } elseif (empty($data['password'])) {
            $this->error("密码不能为空");
        }
        if (!$this->check_verify($data['verify'])) {
            $this->error("验证码错误");
        }
        $res = $model->checkLogined($data);
        if ($res['ok'] == true) {
            $this->success($res['msg'], "Index/index");
        } else {
            $this->error($res['msg']);
        }
    }

    /*验证码*/
    public function Verify()
    {
        $captcha = new Captcha();
        // 设置验证码字符为纯数字
        $captcha->codeSet = '0123456789';
        // 验证码位数
        $captcha->length = 4;
        return $captcha->entry();
    }

    // 检测输入的验证码是否正确，$code为用户输入的验证码字符串，$id多个验证码标识
    function check_verify($code, $id = '')
    {
        $captcha = new Captcha();
        return $captcha->check($code, $id);
    }


    public function loginOut()
    {
        session(null);
        $this->success("您已经成功退出系统", "Login/login");
    }

}

?>