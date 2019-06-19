<?php

namespace app\Admin\Controller;

use think\Controller;

class Nav extends Common
{
    public function nav()
    {
        $search = input("get.search");
        $sel = db("nav");
        $seld = $sel->where('nav_title', 'like', "%$search%")->order("nav_id desc")->paginate(4, false, ['query' => request()->param()]);
        $this->assign(['slct' => $seld]);
        return $this->fetch();
    }

    //导航栏加载添加页面
    public function nav_add()
    {
        return $this->fetch();
    }

    //导航栏执行添加功能
    public function nav_doAdd()
    {
        $accept = input("post.");
        if (empty($accept['nav_title']) || empty($accept['nav_url'])) {
            $this->error("请填写字段内容", "Nav/nav_add", "", 1);
        } else {
            if (db("nav")->insert($accept)) {
                $this->success("添加成功", "Nav/nav", "", 1);
            } else {
                $this->error("添加失败", "Nav/nav_add", "", 1);
            }
        }
    }

    //导航栏加载修改页面
    public function nav_edit()
    {
        $navid = input("param.nav_id");
        $nav = db("nav")->where("nav_id", $navid)->find();
        $this->assign("nav", $nav);
        return $this->fetch();
    }

    //导航栏执行修改功能
    public function nav_doEdit()
    {
        $data = input("post.");
        if (db("nav")->where("nav_title", $data['nav_title'])->where("nav_url", $data['nav_url'])->count() > 0) {
            $this->error("你已经编辑过了，请先编辑内容在提交", "Nav/nav", "", 1);
        } else {
            if (db("nav")->where("nav_id", $data['nav_id'])->update($data)) {
                $this->success("编辑成功", "Nav/nav", "", 1);
            } else {
                $this->error("编辑失败", "Nav/nav", "", 1);
            }
        }
    }

    //导航栏执行单删功能
    public function nav_del()
    {
        $navid = input("post.");
        $navs = db("nav");
        if (empty($navid)) {
            sendmsg(false, "没有获取到ID");
        }
        if ($navs->where("nav_id", $navid)->delete()) {
            sendmsg(true, "删除导航栏成功");
        } else {
            sendmsg(true, "删除导航栏失败");
        }
    }

    //导航栏执行批删功能
    public function nav_delAll()
    {
        $navid = json_decode(input("post.nav_id"));
        if (empty($navid)) {
            sendmsg(false, "没有获取到ID");
        }
        $navs = db("nav");
        if ($navs->where("nav_id", "IN", $navid)->delete()) {
            sendmsg(true, "批量删除成功");
        } else {
            sendmsg(false, "批量删除失败");
        }

    }

}

?>