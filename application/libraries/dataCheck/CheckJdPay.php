<?php
/**
 * 快捷-京东支付
 * @author Administrator
 *
 */
class checkJdPay
{
	private $CI;
	private $config;
	private $logPath = 'application/logs/dataCheck/';
	private $key = 'caika3456';
	public function __construct()
	{
		$this->CI = &get_instance();
        $this->CI->load->model('other_data_check_model', 'dataModel');
	}
	
	public function exec($config, $r_flag = 0)
	{
		$this->config = $config;
		$checkTime = ($config['exc_date'] == '0000-00-00') ? strtotime("-1 day") : strtotime($config['exc_date']) + 86400;
		$checkDate = date('Y-m-d', $checkTime);
		$name = explode('-', $config['name']);
		$locFile = $this->logPath . date('Ymd', $checkTime) . '_jdpay_' . $name[2] . '.csv';
		$locZip = $this->logPath . date('Ymd', $checkTime) . '_jdpay_' . $name[2] . '.zip';
		$bill_date = date('Ymd', $checkTime + 86400);
		$result = $this->download($bill_date, $name[2], $locZip);
		if(!$result)
		{
			$this->CI->dataModel->updateCheckConfig($config['id'], array('fail_times' => $config['fail_times'] + 1));
			if(($config['fail_times'] + 1) >= 3)
			{
				$this->CI->dataModel->insertAlert($config['name'] . '_' . $checkDate, '对账单获取失败报警', $config['name'] . '充值对账单获取失败');
			}
				
			return ;
		}
		//解压对账文件
		$unzipResult = $this->unzip($locZip, $locFile);
		if(!$unzipResult)
		{
			$this->CI->dataModel->insertAlert($config['name'] . '_gs_' . $checkDate, '对账单格式有误报警', $config['name'] . '充值对账单格式有误');
			return;
		}
		unlink($locZip);
		
		if(!file_exists($locFile))
		{
			return ;
		}
		
		//解析对账文件
		$spl_object = new SplFileObject($locFile, 'r');
		if($spl_object)
		{
			$spl_object->seek(filesize($locFile));
			$total = $spl_object->key();
			if($total < 1)
			{
				$this->CI->dataModel->insertAlert($config['name'] . '_gs_' . $checkDate, '对账单格式有误报警', $config['name'] . '充值对账单格式有误');
				return;
			}
			$pSize = 3000;
			$pages = ceil($total/ $pSize);
			$start = 0;
			$trunResult = $this->CI->dataModel->truncateRechargeCheck();
			if(!$trunResult)
			{
				return ;
			}
			$this->CI->dataModel->trans_start('db');
			for($i = 1; $i <= $pages; $i++)
			{
				$datas = array();
				$spl_object->seek($start);
				$num = $pSize;
				while ($num-- && !$spl_object->eof())
				{
					$dataStr = $spl_object->current();
					$data = explode(',', str_replace(array('=', '"'), '', $dataStr));
					if(iconv('GBK', 'UTF-8', trim($data['5'])) != '成功')
					{
						$spl_object->next();
						continue;
					}
					$datas[] = array('trade_no' =>trim($data['0']), 'date' => $checkDate, 'o_status' => 1, 'o_money' => trim($data['2']) * 100);
					$spl_object->next();
				}
				$start += $pSize;
				
				if($datas)
				{
					$res = $this->CI->dataModel->insertRechargeCheck($datas, array('trade_no', 'date', 'o_status', 'o_money'));
					if(!$res)
					{
						$this->CI->dataModel->trans_rollback('db');
						return ;
					}
				}
			}
			//执行比对操作
			$result = $this->CI->dataModel->checkRecharge($checkDate, $this->config, $r_flag);
			if(!$result)
			{
				$this->CI->dataModel->trans_rollback('db');
				return ;
			}
			$this->CI->dataModel->trans_complete('db');
		}
		else
		{
			$this->CI->dataModel->insertAlert($config['name'] . '_gs_' . $checkDate, '对账单格式有误报警', $config['name'] . '充值对账单格式有误');
		}
	}
	
	/**
	 * 下载对账单
	 * @param unknown_type $date
	 * @param unknown_type $merId
	 * @param unknown_type $locZip
	 */
	private function download($date, $merId, $locZip)
	{
		$data = '{name:"'. $date .'bankreturn_'. $merId .'.zip",path:"0001/0002"}';
		$key = $this->key;
		$data=base64_encode($data);
		$md5dataSource=$data.$key;
		$md5data=md5($md5dataSource);
		$params["owner"]= substr($merId, 0, 9);
		$params["data"]=$data;
		$params["md5"]=$md5data;
		$dataString = $this->paddingDataString($params);
		return $this->downLoadFile('http://bapi.jdpay.com/api/download.do', $dataString, $locZip);
	}
	
	/**
	 * 组装http请求数据
	 * @param unknown $data 请求数据
	 * @return string 返回http格式的参数字符串 如a=a&b=b
	 */
	private function paddingDataString($data)
	{
		$linkStr="";
		$isFirst=true;
		foreach($data as $key=>$value)
		{
			if($value!=null && $value!="")
			{
				if(!$isFirst)
				{
					$linkStr.="&";
				}
				$linkStr.=$key."=".urlencode($value);
				if($isFirst)
				{
					$isFirst=false;
				}
			}
				
		}
		
		return $linkStr;
	}
	
	/**
	 * 请求对账文件
	 * @param unknown_type $url
	 * @param unknown_type $data_string
	 * @param unknown_type $fname
	 */
	private function downLoadFile($url,$data_string,$fname)
	{
		$return = false;
		$url=$url."?".$data_string;//对账http接口需要将参数加入url之后，否则接收不到数据(为什么不用post?问对账接口-_-)
		$TIMEOUT = 60;	//超时时间(秒)
		$ch=curl_init();//创建cURL资源
		curl_setopt ( $ch, CURLOPT_TIMEOUT, $TIMEOUT);
		curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, $TIMEOUT-2);
		curl_setopt ( $ch, CURLOPT_POST, 0 );//PHP_curl.dll 5.6.12之前版本设置0.否则接收不到数据
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt($ch, CURLOPT_MAXREDIRS, 7);//HTTp定向级别 ，7最高
		curl_setopt($ch, CURLOPT_HEADER, true);//这里不要header，加块效率
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);//要求结果为字符串且输出到屏幕上
		//如果 CURLOPT_RETURNTRANSFER选项被设置，函数执行成功时会返回执行的结果，失败时返回 FALSE
		$data = curl_exec($ch);
		// 获得响应结果里的：头大小
		$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		// 根据头大小去获取头信息内容
		$header = substr($data, 0, $headerSize);
		$headerTemp=str_replace("\r\n", "&", $header);
		$headerTemp=str_replace("\n", "&", $headerTemp);
		$headerTemp=str_replace("\r", "&", $headerTemp);
		$headerss = explode("&",$headerTemp);
		$return_code="";
		for($i=0;$i<count($headerss);$i++)
		{
			$subS=explode(":",$headerss[$i]);
			if(count($subS)>2 || count($subS)<2)
			{
				continue;
			}
			if($subS[0]=='Return-Code')
			{
				$return_code=$subS[1];
				$return_code=trim($return_code);
			}
		}
	
		if("0000"===$return_code || ""==$return_code)
		{
			//返回编码为成功的，写入文件
			$data=str_replace($header, "", $data);
			if($data)
			{
				$int = file_put_contents($fname, $data);
				if($int)
				{
					$return = true;
				}
			}
		}
		curl_close($ch);
		
		return $return;
	}
	
	/**
	 * 解压zip压缩包
	 * @param unknown_type $locZip
	 * @param unknown_type $locFize
	 */
	private function unzip($locZip, $locFize)
	{
		if ($zip = zip_open($locZip))
		{
			$fileFlag = false;
			while ($zip_entry = zip_read($zip))
			{
				// 打开包
				if (zip_entry_open($zip,$zip_entry,"r"))
				{
					$fstream = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
					@file_put_contents($locFize, $fstream);
					// 关闭入口
					zip_entry_close($zip_entry);
				}
				if(is_file($locFize))
				{
					$fileFlag = true;
					break;
				}
			}
				
			// 关闭压缩包
			zip_close($zip);
				
			return $fileFlag;
		}
	
		return false;
	}
}
