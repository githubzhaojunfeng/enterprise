<?php

namespace app\Admin\Model;

use think\Model;

class NodeModel extends Model
{
    //获取状态为开启的权限列表
    public function getNodeList()
    {
        $data = $this->getNodeByPid(0);
        return $data;
    }

    /*获取所有权限列表*/
    public function getNodeAllList()
    {
        $data = db("node")->select();
        return $data;
    }


    public function getNodeByPid($pid)
    {
        static $datas = [];  //首先声明静态数组，是为了让数据进行保留
        $data = db("node")->where(['pid' => $pid])->select();  //第一次查询是一级菜单
        //dump($data);die;
        //一级菜单遍历，递归进行查询下一级
        foreach ($data as $v) {
            if ($pid == $v['pid']) {   //可以过滤掉其他分类的菜单
                $datas[] = $v;  //将符合要求的数据进行存储
                //dump($datas);die;
                //把查询出来的第一级数据的id 填入，现在在这个ID就是二级菜单的父id
                $this->getNodeByPid($v['id']);  //递归调用
            }
        }

        //返回datas而不是返回data的原因
        //datas是静态数组，data是动态数组，当第二次重新声明data的时候，值就被清空
        return $datas;
    }


    //  /*递归无限极菜单*/
    //  public function getNodeByPid($pid){
    //    //我们先声明一个静态数组
    //    static $datas = [];
    //    $data = db("node")->where(["pid"=>$pid,"status"=>1])->select(); //第一次执行查询一级菜单
    //    foreach($data as $v){
    //      if($pid==$v['pid']){ //如果传过来的pid，跟$v['pid']进行对比，对比成功就将此值存入静态数组
    //        $datas[] = $v;
    //        //开始递归
    //        $this->getNodeByPid($v['id']);
    //      }
    //    }
    //    //返回datas而不是返回data的原因；
    //    //datas是静态数组，data是动态数组，当第二次重新声明data的时候，值就被清空
    //    return $datas;
    // }


    public function addNode($data)
    {
        if (db("node")->insert($data)) {
            return true;
        } else {
            return false;
        }
    }

    /*
     根据权限ID查询权限信息
     */
    public function getNodeById($id)
    {
        $data = db("node")->where("id", $id)->find();
        return $data;
    }

    /*
     执行修改权限
     */

    public function editNode($data)
    {
        if (db("node")->where("id", $data['id'])->update($data)) {
            return true;
        } else {
            return false;
        }
    }

    public function changeStatus($id)
    {
        $data = $this->getNodeById($id);
        if ($data['status'] == 1) {
            $status = 0;
        } else {
            $status = 1;
        }

        if (db("node")->where("id", $id)->setField("status", $status)) {
            return true;
        } else {
            return false;
        }
    }

    public function doDelNode($id)
    {
        $info = $this->getNodeById($id);
        if ($info['level'] == 1) { //如果是一级菜单则需要删除子菜单
            if (db("node")->where("pid", $info['id'])->delete()) {
                if (db("node")->where("id", $id)->delete()) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {  //否则直接删除
            if (db("node")->where("id", $id)->delete()) {
                return true;
            } else {
                return false;
            }
        }

    }

    /*
    批量删除数据
     */
    public function moreDel($id)
    {
        if (db("node")->where("id", "IN", $id)->delete()) {
            return true;
        } else {
            return false;
        }
    }

}

?>