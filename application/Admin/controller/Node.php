<?php

namespace app\Admin\Controller;

use app\admin\model\NodeModel;

class Node extends Common
{
    public function index()
    {
        return $this->fetch();
    }

    public function add()
    {
        return $this->fetch();
    }

    public function doAdd()
    {
        $data = input("post.");
        if ($data['pid'] == 0) {
            $data['level'] = 1;
        } else {
            $data['level'] = 2;
        }
        $nodeModel = new NodeModel();
        if ($nodeModel->addNode($data)) {
            $this->success("添加权限成功", "Node/index", "", 1);
        } else {
            $this->error("添加权限失败", "Node/add", "", 1);
        }
    }

    /*
    修改权限页面渲染
     */
    public function edit($id)
    {
        $nodeModel = new NodeModel();
        $data = $nodeModel->getNodeById($id);
        $this->assign("data", $data);
        return $this->fetch();
    }

    public function doEdit()
    {
        $data = input("post.");
        $nodeModel = new NodeModel();
        if ($nodeModel->editNode($data)) {
            $this->success("修改权限成功", "Node/index", "", 1);
        } else {

            $this->error("修改权限失败", url('Node/edit', ['id' => $data['id']]), "", 1);
        }
    }

    /*
     修改权限的状态
     */
    public function changeStatus()
    {
        $id = input("post.id");
        $status = input("post.status");
        if (empty($id)) {
            sendmsg(false, "没有找到ID");
        }
        $nodeModel = new NodeModel();
        if ($nodeModel->changeStatus($id)) {
            sendmsg(true, "修改权限成功");
        } else {
            sendmsg(false, "修改权限失败");
        }
    }

    /*
    delNode 删除权限
     */
    public function doDel()
    {
        $id = input("post.id");
        if (empty($id)) {
            sendmsg(false, "没有获取到ID");
        }
        $node = new NodeModel();
        if ($node->doDelNode($id)) {
            sendmsg(true, "删除权限成功");
        } else {
            sendmsg(false, "删除权限失败");
        }
    }

    public function doMoreDel()
    {
        $id = json_decode(input("post.id"));
        if (empty($id)) {
            sendmsg(false, "没有获取到ID");
        }

        $nodeModel = new NodeModel();
        if ($nodeModel->moreDel($id)) {
            sendmsg(true, "批量删除成功");
        } else {
            sendmsg(false, "批量删除失败");
        }
    }
}

?>