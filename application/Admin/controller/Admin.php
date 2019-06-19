<?php

namespace app\admin\Controller;

use app\admin\model\AdminModel;
use app\admin\model\RoleModel;
use think\Db;

class Admin extends Common
{
    public function index()
    {
        $model = new AdminModel();
        $keywords = input("get.keywords");
        if (!empty($keywords)) {
            $res = $model->where("username|realname|phone", "like", "%$keywords%")->paginate(4);
        } else {
            $res = $model->paginate(6);
        }
        $param = $this->request->param();
        $res->appends($param);
        $this->assign(['res' => $res, 'page' => $res->render()]);
        return $this->fetch();
        // $data = $model->select();
        // //dump($data);die;
        // foreach($data as $k => $v){
        //   $data[$k]['logo'] = explode(',',$v['logo']);
        // }
        // //dump($data);die;
        // return view('index',['data'=>$data]);
    }



// public function upload(){
//    $model = new AdminModel();
//    $arr = input("post.");
//    $files = request()->file('logo');
//   foreach($files as $file){
//     $info = $file->move(ROOT_PATH . 'public' . DS . 'static' . DS . 'uploads');
//     $data[] = '/uploads/'.$info->getSaveName();
//   }
//   $arr['logo']=implode(',',$data);
//   $res = $model->insert($arr);
//   if($res){
//     $this->success("添加成功","Admin/index");
//   }else{
//     $this->error("添加失败");
//   }

// }


    public function testWhere()
    {
        $username = "tp_admin";
        $username = input("get.username");
        $password = input("get.password");
        //$model = new AdminModel();
        //and 查询 第一种 数组形式 db()是助手函数
        // $where['username'] = input("get.username");
        // $where['password'] = md5(input("get.password"));
        //db()助手函数等同于 Db::table() 区别：助手函数可以使用配置文件中表前缀，Db::table()只能写全部的表明
        //$data = db("admin")->where($where)->find();
        //$data = db::table("admin")->where($where)->find();
        //第二种写法 直接在where()方法中，写数组
        // $data = db("admin")->where(['username'=>$username,"password"=>$password])->find();
        // and查询第三种 字符串形式 如果要查询的知识字符串的话 变量左右加单引号
        //$data = db("admin")->where("username='$username' and password='$password'")->find();

        //查询管理员admin用户拥有哪些权限


        $data = db("admin")->alias("a")
            ->field("a.*,ra.role_id,ac.node_id")
            ->join("role_admin ra", "a.id=ra.user_id")
            ->join("access ac", "a.id=ac.role_id")
            ->where("a.username", $username)->find();
        //dump($data);die;
        $node_id = explode(",", $data['node_id']);
        //In 条件查询的时候 如果是,号隔开的字符串可以进行查询，如果是通过,号拆分为数组也可以进行查询
        $node_list = db("node")->where("id", "IN", $node_id)->select();

    }


    /*add 添加管理员页面渲染*/
    public function add()
    {
        $roleModel = new RoleModel();
        $roles = $roleModel->getTrueRole();
        $this->assign("roles", $roles);
        return $this->fetch();
    }

    /*执行添加 操作数据*/
    public function doAdd()
    {
        $data = input("post.");
        $model = new AdminModel();
        if ($model->getCountAdminByName($data['username']) > 0) {
            $this->error("此账号已存在");
        }

        $data['password'] = md5($data['password']);
        //request()->ip() 客户端向服务端请求时，要发送客户端的IP，所以说我们服务端能取到客户端的IP
        $data['ip'] = request()->ip();
        $file = request()->file("logo");
        //把图片上传至 public/static/uploads
        $info = $file->move(ROOT_PATH . 'public' . DS . 'static' . DS . 'uploads');
        $logo = str_replace("\\", "/", $info->getSaveName());
        $data['logo'] = $logo;
        $ctime = date("Y-m-d H:i:s", time());
        $data['ctime'] = $ctime;
        if ($model->allowField(true)->save($data)) {
            $raData['user_id'] = $model->getLastInsID();
            $raData['role_id'] = $data['role_id'];
            if (db("role_admin")->insert($raData)) {
                $this->success("管理员添加成功", "Admin/index");
            } else {
                $this->error("管理员添加失败", "Admin/add");
            }
        } else {
            $this->error("管理员添加失败", "Admin/add");
        }
    }

    /*执行删除操作*/
    public function doDel()
    {
        $id = input("post.id");
        //dump($id);die;
        $model = new AdminModel();
        if (empty($id)) {
            sendmsg(false, "没有获取到ID");
        }
        if ($model->where("id", $id)->delete()) {
            sendmsg(true, "删除管理员成功");
        } else {
            sendmsg(false, "删除管理员失败");
        }
    }

    /*执行批量删除操作*/
    public function moreDel()
    {
        $id = json_decode(input("post.id"));
        if (empty($id)) {
            sendmsg(false, "没有获取到ID");
        }
        $model = new AdminModel();
        if ($model->where("id", "IN", $id)->delete()) {
            sendmsg(true, "批量删除成功");
        } else {
            sendmsg(false, "批量删除失败");
        }
    }

    public function edit($id)
    {
        $data = AdminModel::get($id);
        //获取原始数据 $data->getData
        $this->assign("data", $data->getData());
        $raData = db("role_admin")->where("user_id", $id)->find();

        $roleModel = new RoleModel();

        $roles = $roleModel->getTrueRole();
        $this->assign(['roles' => $roles, 'role_id' => $raData['role_id']]);
        return $this->fetch();
    }

    public function doEdit()
    {
        $data = input("post.");
        if (!empty($data['password'])) {
            $data['password'] = md5($data['password']);
        } else {
            unset($data['password']);
        }
        $file = request()->file('logo');
        if ($file) {
            $info = $file->move(ROOT_PATH . 'public' . DS . 'static' . DS . 'uploads');
            $logo = str_replace("\\", "/", $info->getSaveName());
            $data['logo'] = $logo;
            $ctime = date("Y-m-d H:i:s", time());
            $data['ctime'] = $ctime;
        }
        $role_id = $data['role_id'];
        unset($data['role_id']);
        $model = new AdminModel();
        $editAdmin = false;
        $editRa = false;
        if ($model->where("id", $data['id'])->update($data)) {
            $editAdmin = true;
        }
        if (db("role_admin")->where("user_id", $data['id'])->setField("role_id", $role_id)) {
            $editRa = true;
        }

        if ($editAdmin == true || $editRa == true) {
            $this->success("修改管理员成功", "Admin/index", "", 1);
        } else {
            $this->error("修改管理员失败", url('Admin/edit', ['id' => $data['id']]), 1);
        }
    }
}

?>