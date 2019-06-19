<?php

namespace app\Admin\Controller;

use app\admin\model\NodeModel;
use think\Controller;

class Common extends Controller
{
    public function _initialize()
    {
        if (empty(session("ADMIN_SESS"))) {
            $this->error("您还没有登录！", "Login/login");
        }
        //dump(session("ADMIN_SESS"));die;
        $this->assign("user", session("ADMIN_SESS"));
        $admin_node = session("ADMIN_NODES");
        //dump($admin_node);die;
        $nodeModel = new NodeModel();
        $node_list = $nodeModel->getNodeList();
        //dump($node_list);die;
        $this->assign(["admin_node" => $admin_node, "node_list" => $node_list]);
    }


}

?>