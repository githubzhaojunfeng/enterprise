<?php

namespace app\Admin\Controller;

use app\admin\model\NodeModel;
use app\admin\model\RoleModel;

class Role extends Common
{
    //列表页
    public function index()
    {
        $role = new RoleModel();
        $data = $role->getRoleList();
        $page = $data->render();
        $this->assign(['data' => $data, "page" => $page]);
        return $this->fetch();
    }

    //添加数据页
    public function add()
    {
        return $this->fetch();
    }


    //执行添加
    public function doAdd()
    {
        $data = input("post.");
        $model = new RoleModel();
        $ctime = date("Y-m-d H:i:s", time());
        $data['ctime'] = $ctime;
        if ($model->doAddRole($data)) {
            $this->success("添加角色成功", "Role/index", "", 1);
        } else {
            $this->error("添加角色失败");
        }
    }


    //修改数据页
    public function edit($id)
    {
        $model = new RoleModel();
        $data = $model->getRoleById($id);
        //var_dump($data);//die;
        $this->assign("data", $data);
        return $this->fetch();
    }

    //执行修改
    public function doEdit()
    {
        $data = input("post.");
        $model = new RoleModel();
        if ($model->doEditRole($data)) {
            $this->success("修改角色成功", "Role/index", "", 1);
        } else {
            $this->error("修改角色失败");
        }
    }

    //执行删除
    public function doDel()
    {
        $id = input("post.id");
        if (empty($id)) {
            sendmsg(false, "ID不能为空");
        }

        $model = new RoleModel();
        if ($model->doDelRole($id)) {
            sendmsg(true, "删除角色成功");
        } else {
            sendmsg(false, "删除角色失败");
        }
    }

    //修改状态
    public function changeStatus()
    {

    }
}

?>