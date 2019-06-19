<?php

namespace app\Admin\Controller;

use think\Controller;
use think\Loader;
use think\Db;

class Excel extends Common
{
    public function excel()
    {
        $data = db("excel")->select();
        $this->assign(['data' => $data]);
        return $this->fetch();
    }


    public function inserExcel()
    {
//       dump(Loader::import('PHPExcel.Classes.PHPExcel'));
//       die;

        Vendor('PHPExcel.Classes.PHPExcel');
        //Vendor('PHPExcel.Classes.PHPExcel.IOFactory');
        Vendor('PHPExcel.Classes.PHPExcel.IOFactory.PHPExcel_IOFactory');

        Vendor('PHPExcel.Classes.PHPExcel.Reader.Excel5');

        //获取表单上传文件
        $file = request()->file('excel');

        $info = $file->validate(['ext' => 'xlsx'])->move(ROOT_PATH . 'public' . DS . 'uploads');

        if ($info) {
            $exclePath = $info->getSaveName();  //获取文件名
            $file_name = ROOT_PATH . 'public' . DS . 'uploads' . DS . $exclePath;   //上传文件的地址
            $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
            $obj_PHPExcel = $objReader->load($file_name, $encode = 'utf-8');  //加载文件内容,编码utf-8
            echo "<pre>";
            $excel_array = $obj_PHPExcel->getsheet(0)->toArray();   //转换为数组格式
            //dump($excel_array);die;
            array_shift($excel_array);  //删除第一个数组(标题);
            $arr = [];
            foreach ($excel_array as $k => $v) {
                $arr[$k]['did'] = $v[0];
                $arr[$k]['real_name'] = $v[1];
                $arr[$k]['cor_name'] = $v[2];
                $arr[$k]['email'] = $v[3];
                $arr[$k]['com_add'] = $v[4];
                $arr[$k]['position'] = $v[5];
                $arr[$k]['tel'] = $v[6];
                $arr[$k]['role_id'] = $v[7];
                // $arr[$k]['active'] = $v[8];
                $arr[$k]['apphang'] = $v[8];


            }
            Db::name('excel')->insertAll($arr); //批量插入数据
        } else {
            echo $file->getError();
        }

    }


    public function daochu()
    {
        $xlsData = Db('excel')->select();
        Vendor('PHPExcel.Classes.PHPExcel');//调用类库,路径是基于vendor文件夹的
        Vendor('PHPExcel.Classes.PHPExcel.Worksheet.Drawing');
        Vendor('PHPExcel.Classes.PHPExcel.Writer.Excel2007');
        $objExcel = new \PHPExcel();
        //set document Property
        $objWriter = \PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
        $objActSheet = $objExcel->getActiveSheet();
        $key = ord("A");
        $letter = explode(',', "A,B,C,D,E,F,G,H,I");

        $arrHeader = array('姓名', '公司', '公司地址', '邮箱', '电话', '职位', '行业应用', '会员角色', '是否验证');
        //填充表头信息
        $lenth = count($arrHeader);
        for ($i = 0; $i < $lenth; $i++) {
            $objActSheet->setCellValue("$letter[$i]1", "$arrHeader[$i]");
        };
        //填充表格信息
        foreach ($xlsData as $k => $v) {
            $k += 2;
            $objActSheet->setCellValue('A' . $k, $v['real_name']);
            $objActSheet->setCellValue('B' . $k, $v['cor_name']);
            $objActSheet->setCellValue('C' . $k, $v['com_add']);
            $objActSheet->setCellValue('D' . $k, $v['email']);
            $objActSheet->setCellValue('E' . $k, $v['tel']);
            $objActSheet->setCellValue('F' . $k, $v['position']);
            $objActSheet->setCellValue('G' . $k, $v['apphang']);
            $objActSheet->setCellValue('H' . $k, $v['role_id'] == 2 ? '正式会员' : '普通会员');
            $objActSheet->setCellValue('I' . $k, $v['active'] == 1 ? '是' : '否');


            // $objActSheet->setCellValue('A'.$k,$v['did']);
            // $objActSheet->setCellValue('B'.$k,$v['real_name']);
            // $objActSheet->setCellValue('C'.$k, $v['cor_name']);
            // $objActSheet->setCellValue('D'.$k, $v['com_add']);
            // $objActSheet->setCellValue('E'.$k, $v['email']);
            // $objActSheet->setCellValue('F'.$k, $v['tel']);
            // $objActSheet->setCellValue('G'.$k, $v['position']);
            // $objActSheet->setCellValue('H'.$k, $v['apphang']);
            // $objActSheet->setCellValue('I'.$k, $v['role_id'] == 2?'正式会员':'普通会员');
            // $objActSheet->setCellValue('J'.$k, $v['active'] == 1?'是':'否');
            // 表格高度
            $objActSheet->getRowDimension($k)->setRowHeight(20);
        }

        $width = array(10, 15, 20, 25, 30);
        //设置表格的宽度
        $objActSheet->getColumnDimension('A')->setWidth($width[1]);
        $objActSheet->getColumnDimension('B')->setWidth($width[2]);
        $objActSheet->getColumnDimension('C')->setWidth($width[3]);
        $objActSheet->getColumnDimension('D')->setWidth($width[4]);
        $objActSheet->getColumnDimension('E')->setWidth($width[1]);
        $objActSheet->getColumnDimension('F')->setWidth($width[1]);
        $objActSheet->getColumnDimension('G')->setWidth($width[1]);
        $objActSheet->getColumnDimension('H')->setWidth($width[1]);
        $objActSheet->getColumnDimension('I')->setWidth($width[1]);


        $outfile = "mysql数据库列表.xlsx";
        ob_end_clean();
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="' . $outfile . '"');
        header("Content-Transfer-Encoding: binary");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $objWriter->save('php://output');
    }
}

?>