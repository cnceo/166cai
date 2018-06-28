<?php

/**
 * Copyright (c) 2016,上海二三四五网络科技股份有限公司
 * 摘    要：投注站管理
 * 作    者：liuz@2345.com
 * 修改日期：2016.01.19
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class BetStation extends MY_Controller
{
    private $path = '../uploads/partner/';
    private $_lids; 
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('model_betstation');
        $this->_lids = $this->config->item('cfg_partner_lid');
    }

    /**
     * 参    数：无
     * 作    者：liuz
     * 功    能：投注站管理
     * 修改日期：2016.01.19
     */
    public function index()
    {
        $this->check_capacity('11_1_1');
        $page = intval($this->input->get("p"));
        $page = $page <= 1 ? 1 : $page;
        $num = 100;
        $fromType = $this->input->get("fromType", TRUE);
        $searchData = array(
            "partnerId" => $this->input->post("partnerId", TRUE),
            "shopId"   => $this->input->post("shopId", TRUE),
        	"lid"   => $this->input->post("lid", TRUE),
        );
        $datas = $this->model_betstation->getList($searchData, $page, $num);
        $partnerId = $this->model_betstation->getPartnerId('cp_partner');
        $pageConfig = array(
            "page"     => $page,
            "npp"      => $num,
            "allCount" => $datas[1]
        );
        $pages = get_pagination($pageConfig);
        $info = array(
            "datas" => $datas[0],
            "fromType" => $fromType,
            "pages"    => $pages,
            "search"   => $searchData,
            "partnerId"   => $partnerId,
        	"lids"      => $this->_lids
        );
        $this->load->view("BetStation/index", $info);
    }

    public function BetStationDetail()
    {
        $searchData = array(
            "id" => $this->input->get("id", TRUE),
        );
        $datas = $this->model_betstation->getOne($searchData, 'cp_partner_shop');
        $files = $this->model_betstation->getOne($searchData, 'cp_partner_shop_file');
        $info = array(
            "datas" => $datas,
            "files" => $files,
        	"lids"  => $this->_lids
        );
        $this->load->view('BetStation/betStationDetail', $info);
    }

    public function BetStationEdit()
    {
        $this->check_capacity('11_1_2');
        $searchData = array(
            "id"   => $this->input->get("id", TRUE),
        );
        $datas = $this->model_betstation->getOne($searchData, 'cp_partner_shop');
        $files = $this->model_betstation->getOne($searchData, 'cp_partner_shop_file');

        $info = array(
            "datas" => $datas,
            "files" => $files,
        	"lids"  => $this->_lids,
//            "num" => $num[0],
        );
        $this->load->view('BetStation/betStationEdit', $info);
    }

    public function getNum()
    {
        $searchData = array(
            "id"   => $this->input->post("id", TRUE),
        );
        $num = $this->model_betstation->getNum($searchData, 'cp_partner_shop_file');
        $info = array('num'    => $num[0]['num'],);
        die(json_encode($info));
    }

    public function uploadFile()
    {
        $upData = array(
            "partnerId" => $this->input->get("pid", TRUE),
            "shopId" => $this->input->get("id", TRUE),
        );
        $file = BASEPATH.$this->path;
        if(!file_exists($file))
        {
            mkdir($file);
        }
        $file .= $upData['partnerId']."/";
        if(!file_exists($file))
        {
            mkdir($file);
        }
        if(isset($_FILES['file']['name']))
        {
            $hname = pathinfo ( $_FILES['file']['name'], PATHINFO_EXTENSION );
        }
        $filename = $file.$upData['shopId']."_".time().rand(0001,9999).".".$hname;
        $type = move_uploaded_file($_FILES["file"]["tmp_name"],$filename );
        if($type == false)
        {
            echo  "上传的文件不符合要求";
        }
        else
        {
            if(isset($filename))
            {
                $strName = strrev($filename);
                $sname = explode('/', $strName);
                $sname = strrev($sname[0]);
            }
            $upData['filename'] = str_replace("&","",$_FILES['file']['name']);
            $upData['filepath'] = $sname;
            $res = array (
                'filename' => $upData['filename'],
                'filepath' => $upData['filepath']
            );
            exit ( json_encode ( $res ) );
        }

    }

    public function BetStationUpload()
    {
        $searchData = array(
            "partnerId" => $this->input->post("partnerId", TRUE),
            "id" => $this->input->post("id", TRUE),
            "shopNum"   => $this->input->post("bid", TRUE),
            "cname" => $this->input->post("cname", TRUE),
            "lottery_type" => $this->input->post("lottery_type", TRUE),
            "phone"   => $this->input->post("phone", TRUE),
            "qq" => $this->input->post("qq", TRUE),
            "webchat"   => $this->input->post("wechat", TRUE),
            "other_contact" => $this->input->post("othercontact", TRUE),
            "address"   => $this->input->post("address", TRUE),
        	"lid"   => $this->input->post("lid", TRUE)
        );
        $pdata = $this->model_betstation->getPartner('cp_partner_shop');
        $filename = $this->input->post ( 'filename', TRUE);
        $filepath = $this->input->post ( 'filepath', TRUE );
       	if(!empty($filename))
        {
	        foreach ( $filename as $k => $file )
	        {
	            $files [$k] ['filename'] = $file;
	            $files [$k] ['filepath'] = $filepath[$k];
	        }
	        $this->model_betstation->upload($searchData, $files);
        }
        foreach($pdata as $key => $value)
        {
             if($searchData['partnerId'] === $value['partnerId'] && $searchData['shopId'] === $value['shopNum'] && $searchData['id'] !== $value['id'])
             {
                 $res = array(
                     'status' => '00',
                     'msg'    => '投注站编号重复',
                 );
                 die(json_encode($res));
             }
        }
        $datas = $this->model_betstation->updateData($searchData);
        if($datas)
        {
            $res = array(
                'status' => '01',
                'msg'    => '更新成功',
            );
        }
        die(json_encode($res));
    }

    public function download()
    {
        header("Content-type:text/html;charset=utf-8");
        $searchData = array(
            "id"   => $this->input->get("id", TRUE),
        );
        $datas = $this->model_betstation->download($searchData, 'cp_partner_shop_file');
        foreach($datas as $data)
        {
            $partnerId = $data['partnerId'];
            $path_name = $data['filepath'];
        }
        $path_name = $this->path.$partnerId.'/'.$path_name;
        $save_name = basename($path_name);
        $hfile = @fopen($path_name, "rb") or die("Can not find file: $path_name\n");
        Header("Content-type: application/octet-stream");
        Header("Content-Transfer-Encoding: binary");
        Header("Accept-Ranges: bytes");
        Header("Content-Length: ".filesize($path_name));
        Header("Content-Disposition: attachment; filename = \"$save_name\"");
        while (!feof($hfile))
        {
              echo fread($hfile, 32768);
        }
        fclose($hfile);
    }


    public function deleteFile()
    {
        $data = $this->input->post('data',true);
        $datas = json_decode($data, true);
        foreach($datas as $value)
        {
            $filedatas = $this->model_betstation->deleteFile($value, 'cp_partner_shop_file');
        }
       return $filedatas;
    }

    public function BetStationUpdate()
    {
        $this->check_capacity('11_1_3');
        $searchData = array(
            "id" => $this->input->post("id", TRUE),
            "status" => $this->input->post("status", TRUE),
        );
        $reason = $this->input->post("reason", TRUE);
        $name = $this->input->post("name", TRUE);
        if(!empty($reason))
        {
           $searchData['status'] == 10 ? $searchData['fail_reason'] = $reason : $searchData['off_reason'] = $reason;
            $datas = $this->model_betstation->unPass($searchData);
            $searchData['status'] == 10 ? $this->syslog(25, "审核不通过合作商".$name.",原因:".$reason) : $this->syslog(25, "下架合作商".$name.",原因:".$reason);
        }
        else
        {
            $datas = $this->model_betstation->updateStatus($searchData);
            $searchData['status'] == 20 ? $this->syslog(25, "审核通过合作商".$name) : $this->syslog(25, "上架合作商".$name);

        }
        echo 1;
    }

}
