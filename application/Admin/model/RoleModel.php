<?php

namespace app\Admin\Model;

use think\Model;

class RoleModel extends Model
{

    public function getRoleList()
    {
        $data = db("role")->paginate(10);
        return $data;
    }

    //执行添加
    public function doAddRole($data)
    {
        $roleData = $data; //将数据分隔
        unset($roleData['node_id']); //由于角色表没有权限ID所以要删除
        $nodeData['node_id'] = implode(",", $data['node_id']); //将权限ID以逗号分隔，插入数据库
        if (db("role")->insert($roleData)) { //首先判断角色是否新添成功
            //获取某一张表上一次插入值的主键ID
            $nodeData['role_id'] = db("role")->getLastInsID();
            if (db("access")->insert($nodeData)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    //执行修改
    public function doEditRole($data)
    {
        $roleData = $data;
        unset($roleData['node_id']);
        $nodeData['node_id'] = implode(",", $data['node_id']); //将权限ID以逗号分隔，插入数据库

        $editRoleStatus = false;
        $editAccessStatus = false;

        if (db("role")->where("id", $data['id'])->update($roleData)) {
            $editRoleStatus = true;
        }

        if (db("access")->where("role_id", $data['id'])->setField("node_id", $nodeData['node_id'])) {
            $editAccessStatus = true;
        }
        //两个条件修改一个成功即可，因为我们一次操作的是两张数据表 
        if ($editRoleStatus == true || $editAccessStatus == true) {
            return true;
        } else {
            return false;
        }
    }

    //执行删除
    public function doDelRole($id)
    {
        if (db("role")->where("id", $id)->delete()) {
            return true;
        } else {
            return false;
        }
    }

    //修改状态
    public function changeStatus()
    {

    }

    /*
     getRoleById 通过角色Id查询角色信息 
     $id 角色ID
     */
    public function getRoleById($id)
    {
        $data = db("role")->where("id", $id)->find();
        $node = db("access")->where("role_id", $id)->find();
        $data['node_id'] = explode(",", $node['node_id']); //将权限ID以逗号分隔，插入数据库
        return $data;
    }

    public function getTrueRole()
    {
        $data = db("role")->where("status", 1)->select();
        return $data;
    }
}

?>